<?php

namespace App\Http\Controllers\Backend;

use App\AcademicYear;
use App\Expense;
use App\FeePayment;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\StudentLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceReportController extends Controller
{
    public function index(Request $request)
    {
        $academicYearId = $request->query->get('academic_year', AppHelper::getAcademicYear());
        $month = $request->query->get('month', date('Y-m'));

        $monthStart = Carbon::parse($month . '-01')->startOfMonth();
        $monthEnd = Carbon::parse($month . '-01')->endOfMonth();

        $revenueMonth = FeePayment::whereBetween('payment_date', [$monthStart, $monthEnd])
            ->when($academicYearId, function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->sum('total_amount');

        $expensesMonth = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $outstandingArrears = StudentLedger::where('status', AppHelper::ACTIVE)
            ->when($academicYearId, function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->where('balance', '>', 0)
            ->sum('balance');

        $totalCredit = abs(StudentLedger::where('status', AppHelper::ACTIVE)
            ->when($academicYearId, function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->where('balance', '<', 0)
            ->sum('balance'));

        $driver = DB::getDriverName();
        $monthPaymentExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', payment_date)"
            : "DATE_FORMAT(payment_date, '%Y-%m')";
        $monthExpenseExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', expense_date)"
            : "DATE_FORMAT(expense_date, '%Y-%m')";

        $monthlyRevenue = FeePayment::select(
            DB::raw("{$monthPaymentExpr} as month"),
            DB::raw('SUM(total_amount) as total')
        )
            ->when($academicYearId, function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $monthlyExpenses = Expense::select(
            DB::raw("{$monthExpenseExpr} as month"),
            DB::raw('SUM(amount) as total')
        )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $expenseByCategory = Expense::select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
            ->groupBy('expense_category_id')
            ->with('category')
            ->get();

        $academicYears = AcademicYear::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->pluck('title', 'id');

        return view('backend.finance.report.index', compact(
            'academicYears', 'academicYearId', 'month',
            'revenueMonth', 'expensesMonth', 'outstandingArrears', 'totalCredit',
            'monthlyRevenue', 'monthlyExpenses', 'expenseByCategory'
        ));
    }
}
