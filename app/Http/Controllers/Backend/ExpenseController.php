<?php

namespace App\Http\Controllers\Backend;

use App\Expense;
use App\ExpenseCategory;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['hiddenId' => 'required|integer']);
            Expense::findOrFail($request->get('hiddenId'))->delete();

            return redirect()->route('finance.expense.index')->with('success', 'Expense deleted.');
        }

        $categoryId = $request->query->get('category', 0);
        $month = $request->query->get('month', date('Y-m'));

        $expenses = Expense::with('category')
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('expense_category_id', $categoryId);
            })
            ->when($month, function ($q) use ($month) {
                $start = Carbon::parse($month . '-01')->startOfMonth();
                $end = Carbon::parse($month . '-01')->endOfMonth();
                $q->whereBetween('expense_date', [$start, $end]);
            })
            ->orderBy('expense_date', 'desc')
            ->paginate(env('MAX_RECORD_PER_PAGE', 25));

        $categories = ExpenseCategory::where('status', AppHelper::ACTIVE)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return view('backend.finance.expense.list', compact('expenses', 'categories', 'categoryId', 'month'));
    }

    public function create(Request $request, $id = 0)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'expense_category_id' => 'required|integer|exists:expense_categories,id',
                'expense_date' => 'required|date_format:d/m/Y',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|max:500',
                'reference_no' => 'nullable|max:100',
                'payment_method' => 'nullable|in:' . implode(',', array_keys(AppHelper::PAYMENT_METHODS)),
            ]);

            $data = $request->only([
                'expense_category_id', 'amount', 'description', 'reference_no', 'payment_method',
            ]);
            $data['expense_date'] = Carbon::createFromFormat('d/m/Y', $request->get('expense_date'));

            Expense::updateOrCreate(['id' => $id], $data);

            return redirect()->route('finance.expense.index')
                ->with('success', $id ? 'Expense updated.' : 'Expense added.');
        }

        $expense = Expense::find($id);
        $categories = ExpenseCategory::where('status', AppHelper::ACTIVE)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        return view('backend.finance.expense.add', compact('expense', 'categories'));
    }

    public function chart(Request $request)
    {
        $month = $request->query->get('month', date('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = Carbon::parse($month . '-01')->endOfMonth();

        $chartData = Expense::select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->whereBetween('expense_date', [$start, $end])
            ->groupBy('expense_category_id')
            ->with('category')
            ->get();

        $labels = $chartData->map(function ($row) {
            return $row->category ? $row->category->name : 'Unknown';
        })->values();
        $values = $chartData->pluck('total')->values();
        $total = $chartData->sum('total');

        return view('backend.finance.expense.chart', compact('labels', 'values', 'total', 'month'));
    }
}
