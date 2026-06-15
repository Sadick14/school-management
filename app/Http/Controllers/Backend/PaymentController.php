<?php

namespace App\Http\Controllers\Backend;

use App\AcademicYear;
use App\FeePayment;
use App\FeeStructure;
use App\FeeType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\IClass;
use App\Registration;
use App\Services\BillingService;
use App\StudentLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class PaymentController extends Controller
{
    protected $billing;

    public function __construct(BillingService $billing)
    {
        $this->billing = $billing;
    }

    public function index(Request $request)
    {
        $academicYearId = $request->query->get('academic_year', AppHelper::getAcademicYear());
        $payments = FeePayment::with(['student', 'registration.class', 'items.ledger.feeType'])
            ->when($academicYearId, function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->orderBy('id', 'desc')
            ->paginate(env('MAX_RECORD_PER_PAGE', 25));

        $academicYears = AcademicYear::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->pluck('title', 'id');

        $classes = IClass::where('status', AppHelper::ACTIVE)
            ->orderBy('order', 'asc')
            ->pluck('name', 'id');

        return view('backend.finance.payment.list', compact('payments', 'academicYears', 'academicYearId', 'classes'));
    }

    public function wizard()
    {
        $academicYears = AcademicYear::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->pluck('title', 'id');
        $classes = IClass::where('status', AppHelper::ACTIVE)
            ->orderBy('order', 'asc')
            ->pluck('name', 'id');

        return view('backend.finance.payment.wizard', compact('academicYears', 'classes'));
    }

    public function getStudents(Request $request)
    {
        $this->validate($request, [
            'academic_year_id' => 'required|integer',
            'class_id' => 'required|integer',
            'section_id' => 'nullable|integer',
        ]);

        $students = Registration::where('academic_year_id', $request->get('academic_year_id'))
            ->where('class_id', $request->get('class_id'))
            ->section($request->get('section_id'))
            ->where('status', AppHelper::ACTIVE)
            ->with(['student' => function ($q) {
                $q->select('id', 'name', 'student_id');
            }, 'section'])
            ->orderBy('roll_no', 'asc')
            ->get()
            ->map(function ($reg) {
                return [
                    'id' => $reg->id,
                    'student_id' => $reg->student_id,
                    'name' => $reg->student ? $reg->student->name : 'N/A',
                    'roll_no' => $reg->roll_no,
                    'section' => $reg->section ? $reg->section->name : '',
                    'regi_no' => $reg->regi_no,
                ];
            });

        return response()->json($students);
    }

    public function searchStudents(Request $request)
    {
        $this->validate($request, [
            'q' => 'required|string|min:1|max:100',
            'class_id' => 'nullable|integer',
        ]);

        $term = $request->get('q');
        $academicYearId = AppHelper::getAcademicYear();

        $students = Registration::where('status', AppHelper::ACTIVE)
            ->when($academicYearId, function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->when($request->get('class_id'), function ($q) use ($request) {
                $q->where('class_id', $request->get('class_id'));
            })
            ->where(function ($q) use ($term) {
                $q->where('regi_no', 'like', "%{$term}%")
                    ->orWhereHas('student', function ($sq) use ($term) {
                        $sq->where('name', 'like', "%{$term}%");
                    });
            })
            ->with(['student:id,name', 'class:id,name', 'section:id,name'])
            ->orderBy('regi_no')
            ->limit(15)
            ->get()
            ->map(function ($reg) {
                return [
                    'id' => $reg->id,
                    'student_id' => $reg->student_id,
                    'academic_year_id' => $reg->academic_year_id,
                    'name' => $reg->student ? $reg->student->name : 'N/A',
                    'regi_no' => $reg->regi_no,
                    'class' => $reg->class ? $reg->class->name : '',
                    'section' => $reg->section ? $reg->section->name : '',
                ];
            });

        return response()->json($students);
    }

    public function getDues(Request $request)
    {
        $this->validate($request, [
            'registration_ids' => 'required|array|min:1',
            'registration_ids.*' => 'integer|exists:registrations,id',
            'feeding_from' => 'nullable|date',
            'feeding_to' => 'nullable|date',
            'optional_fees' => 'nullable|array',
            'optional_fees.*' => 'integer|exists:fee_types,id',
        ]);

        $registrationIds = $request->get('registration_ids');
        $feedingFrom = $request->get('feeding_from');
        $feedingTo = $request->get('feeding_to');

        if ($request->get('optional_fees')) {
            foreach ($registrationIds as $regId) {
                foreach ($request->get('optional_fees') as $feeTypeId) {
                    $this->billing->createOptionalFeeLedger($regId, $feeTypeId);
                }
            }
        }

        $ledgers = $this->billing->prepareDuesForRegistrations(
            $registrationIds,
            $feedingFrom,
            $feedingTo
        );

        $optionalAvailable = $this->getOptionalFeesForRegistrations($registrationIds);

        $registrations = Registration::whereIn('id', $registrationIds)->get();
        $activeTerm = null;
        if ($registrations->isNotEmpty()) {
            $activeTerm = AppHelper::getActiveTerm($registrations->first()->academic_year_id);
        }

        $grouped = $ledgers->groupBy('registration_id')->map(function ($items, $regId) {
            $registration = $items->first()->registration;
            return [
                'registration_id' => $regId,
                'student_name' => $registration && $registration->student ? $registration->student->name : '',
                'regi_no' => $registration ? $registration->regi_no : '',
                'items' => $items->map(function ($ledger) {
                    return [
                        'ledger_id' => $ledger->id,
                        'fee_type' => $ledger->feeType ? $ledger->feeType->name : '',
                        'fee_type_code' => $ledger->feeType ? $ledger->feeType->code : null,
                        'description' => $ledger->description,
                        'term' => $ledger->term ? $ledger->term->name : '',
                        'billing_date' => $ledger->billing_date ? $ledger->billing_date->format('d/m/Y') : '',
                        'amount' => (float) $ledger->amount,
                        'amount_paid' => (float) $ledger->amount_paid,
                        'balance' => (float) $ledger->balance,
                        'is_credit' => $ledger->balance < 0,
                    ];
                })->values(),
                'total_expected' => round($items->sum('amount'), 2),
                'total_paid' => round($items->sum('amount_paid'), 2),
                'total_due' => round($items->where('balance', '>', 0)->sum('balance'), 2),
                'total_credit' => round(abs($items->where('balance', '<', 0)->sum('balance')), 2),
            ];
        })->values();

        return response()->json([
            'students' => $grouped,
            'optional_fees' => $optionalAvailable,
            'active_term' => $activeTerm ? $activeTerm->name : null,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'academic_year_id' => 'required|integer',
            'registration_id' => 'required|integer|exists:registrations,id',
            'student_id' => 'required|integer|exists:students,id',
            'payment_date' => 'required|date_format:d/m/Y',
            'payment_method' => 'required|in:' . implode(',', array_keys(AppHelper::PAYMENT_METHODS)),
            'paid_by' => 'nullable|max:255',
            'note' => 'nullable|max:1000',
            'items' => 'required|array|min:1',
            'items.*.ledger_id' => 'required|integer|exists:student_ledgers,id',
            'items.*.amount' => 'required|numeric|min:0.01',
        ]);

        $totalAmount = collect($request->get('items'))->sum('amount');
        if ($totalAmount <= 0) {
            return response()->json(['success' => false, 'message' => 'Total payment must be greater than zero.'], 422);
        }

        try {
            $payment = $this->billing->applyPayment([
                'payment_date' => Carbon::createFromFormat('d/m/Y', $request->get('payment_date')),
                'academic_year_id' => $request->get('academic_year_id'),
                'registration_id' => $request->get('registration_id'),
                'student_id' => $request->get('student_id'),
                'total_amount' => $totalAmount,
                'payment_method' => $request->get('payment_method'),
                'paid_by' => $request->get('paid_by'),
                'note' => $request->get('note'),
            ], $request->get('items'));

            $registrationId = $request->get('registration_id');
            $this->activateRegistrationIfPaid($registrationId);

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'receipt_url' => route('finance.payment.receipt', $payment->id),
                'receipt_no' => $payment->receipt_no,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function receipt(Request $request, $id)
    {
        $payment = FeePayment::with([
            'items.ledger.feeType',
            'student',
            'registration.class',
            'registration.section',
            'academicYear',
            'creator',
        ])->findOrFail($id);

        $appSettings = AppHelper::getAppSettings();
        $activeTerm = AppHelper::getActiveTerm($payment->academic_year_id);

        $pdf = PDF::loadView('backend.finance.payment.receipt_print', compact('payment', 'appSettings', 'activeTerm'))
            ->setPaper('a5', 'portrait');

        $filename = 'receipt-' . $payment->receipt_no . '.pdf';

        if ($request->query('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    public function history(Request $request)
    {
        $this->validate($request, [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'nullable|integer',
        ]);

        $academicYearId = $request->get('academic_year_id');
        $classId = $request->get('class_id');

        $registrations = Registration::where('academic_year_id', $academicYearId)
            ->where('class_id', $classId)
            ->section($request->get('section_id'))
            ->where('status', AppHelper::ACTIVE)
            ->with(['student', 'class', 'section'])
            ->orderBy('roll_no', 'asc')
            ->get();

        $payments = FeePayment::with('items.ledger.feeType')
            ->whereIn('registration_id', $registrations->pluck('id'))
            ->orderBy('payment_date', 'asc')
            ->get()
            ->groupBy('registration_id');

        $academicYear = AcademicYear::find($academicYearId);
        $appSettings = AppHelper::getAppSettings();

        $pdf = PDF::loadView('backend.finance.payment.history_print', compact('registrations', 'payments', 'academicYear', 'appSettings'))
            ->setPaper('a4', 'portrait');

        $className = $registrations->first() && $registrations->first()->class ? $registrations->first()->class->name : 'class';
        $filename = 'payment-history-' . str_replace(' ', '-', $className) . '.pdf';

        if ($request->query('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    public function edit($id)
    {
        $payment = FeePayment::with([
            'items.ledger.feeType',
            'student',
            'registration.class',
        ])->findOrFail($id);

        $ledgers = collect();
        if ($payment->registration_id) {
            $ledgers = StudentLedger::with('feeType')
                ->where('registration_id', $payment->registration_id)
                ->where('academic_year_id', $payment->academic_year_id)
                ->where('status', AppHelper::ACTIVE)
                ->get();
        }

        return view('backend.finance.payment.edit', compact('payment', 'ledgers'));
    }

    public function update(Request $request, $id)
    {
        $payment = FeePayment::with('items')->findOrFail($id);

        $this->validate($request, [
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:fee_payment_items,id',
            'items.*.student_ledger_id' => 'required|integer|exists:student_ledgers,id',
        ]);

        DB::transaction(function () use ($request, $payment) {
            foreach ($request->get('items') as $itemData) {
                $item = $payment->items->firstWhere('id', (int) $itemData['id']);
                $newLedgerId = (int) $itemData['student_ledger_id'];

                if (!$item || $newLedgerId == $item->student_ledger_id) {
                    continue;
                }

                $newLedger = StudentLedger::lockForUpdate()
                    ->where('registration_id', $payment->registration_id)
                    ->find($newLedgerId);

                if (!$newLedger) {
                    continue;
                }

                $oldLedger = StudentLedger::lockForUpdate()->find($item->student_ledger_id);
                if ($oldLedger) {
                    $oldLedger->amount_paid = bcsub($oldLedger->amount_paid, $item->amount_applied, 2);
                    $oldLedger->balance = bcsub($oldLedger->amount, $oldLedger->amount_paid, 2);
                    $oldLedger->save();
                }

                $newLedger->amount_paid = bcadd($newLedger->amount_paid, $item->amount_applied, 2);
                $newLedger->balance = bcsub($newLedger->amount, $newLedger->amount_paid, 2);
                $newLedger->save();

                $item->student_ledger_id = $newLedgerId;
                $item->save();
            }
        });

        return redirect()->route('finance.payment.index')->with('success', 'Payment updated.');
    }

    public function generateBilling(Request $request)
    {
        $this->validate($request, [
            'academic_year_id' => 'required|integer',
            'term_id' => 'required|integer',
            'class_id' => 'nullable|integer',
        ]);

        $count = $this->billing->generateTermFees(
            $request->get('academic_year_id'),
            $request->get('term_id'),
            $request->get('class_id')
        );

        return redirect()->back()->with('success', "{$count} term fee ledger entries generated.");
    }

    protected function activateRegistrationIfPaid($registrationId)
    {
        $registration = Registration::find($registrationId);
        if (!$registration || $registration->status == AppHelper::ACTIVE) {
            return;
        }

        $registrationFeeType = FeeType::where('code', 'REGISTRATION')->first();
        if (!$registrationFeeType) {
            return;
        }

        $unpaidBalance = StudentLedger::where('registration_id', $registrationId)
            ->where('fee_type_id', $registrationFeeType->id)
            ->where('balance', '>', 0)
            ->sum('balance');

        if ($unpaidBalance <= 0) {
            $registration->update(['status' => AppHelper::ACTIVE]);
        }
    }

    protected function getOptionalFeesForRegistrations(array $registrationIds)
    {
        $registrations = Registration::whereIn('id', $registrationIds)->get();
        if ($registrations->isEmpty()) {
            return [];
        }

        $academicYearId = $registrations->first()->academic_year_id;
        $optionalTypes = FeeType::where('is_optional', 1)
            ->where('status', AppHelper::ACTIVE)
            ->get();

        $available = [];
        foreach ($optionalTypes as $type) {
            $hasStructure = FeeStructure::where('academic_year_id', $academicYearId)
                ->where('fee_type_id', $type->id)
                ->where('status', AppHelper::ACTIVE)
                ->exists();

            if (!$hasStructure) {
                continue;
            }

            $alreadyBilled = StudentLedger::whereIn('registration_id', $registrationIds)
                ->where('fee_type_id', $type->id)
                ->where('academic_year_id', $academicYearId)
                ->count();

            if ($alreadyBilled < count($registrationIds)) {
                $available[] = [
                    'id' => $type->id,
                    'name' => $type->name,
                    'code' => $type->code,
                ];
            }
        }

        return $available;
    }

    public function getClassStudents(Request $request)
    {
        $classId = $request->get('class_id');
        $academicYearId = AppHelper::getAcademicYear();

        $students = Registration::where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->with('student')
            ->get(['id', 'student_id', 'regi_no'])
            ->map(function ($reg) {
                return [
                    'id' => $reg->student_id,
                    'name' => $reg->student->name,
                    'regi_no' => $reg->regi_no,
                ];
            });

        return response()->json(['success' => true, 'students' => $students]);
    }

    public function recordBulkPayments(Request $request)
    {
        $classId = $request->get('class_id');
        $payments = $request->get('payments', []);
        $academicYearId = AppHelper::getAcademicYear();

        if (!$classId || !count($payments)) {
            return response()->json(['success' => false, 'message' => 'No payments provided'], 422);
        }

        try {
            $recorded = 0;

            foreach ($payments as $payment) {
                $studentId = $payment['student_id'];
                $amount = (float) $payment['amount'];
                $paymentDate = Carbon::createFromFormat('d/m/Y', $payment['payment_date']);
                $paymentMethod = $payment['payment_method'];

                $registration = Registration::where('student_id', $studentId)
                    ->where('class_id', $classId)
                    ->where('academic_year_id', $academicYearId)
                    ->first();

                if (!$registration) {
                    continue;
                }

                $feePayment = FeePayment::create([
                    'receipt_no' => app('App\Services\BillingService')->generateReceiptNo(),
                    'payment_date' => $paymentDate,
                    'academic_year_id' => $academicYearId,
                    'registration_id' => $registration->id,
                    'student_id' => $studentId,
                    'total_amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'paid_by' => 'Bulk Record',
                    'note' => 'Recorded from past payments',
                ]);

                $ledgers = StudentLedger::where('registration_id', $registration->id)
                    ->where('balance', '>', 0)
                    ->orderBy('term_id', 'asc')
                    ->orderBy('billing_date', 'asc')
                    ->orderBy('fee_type_id', 'asc')
                    ->get();

                foreach ($ledgers as $ledger) {
                    if ($amount <= 0) break;

                    $amountApplied = min($amount, $ledger->balance);
                    $ledger->update([
                        'amount_paid' => $ledger->amount_paid + $amountApplied,
                        'balance' => $ledger->balance - $amountApplied,
                    ]);

                    $feePayment->items()->create([
                        'student_ledger_id' => $ledger->id,
                        'amount_applied' => $amountApplied,
                    ]);

                    $amount -= $amountApplied;
                }

                $recorded++;
            }

            return response()->json([
                'success' => true,
                'message' => $recorded . ' payment(s) recorded successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
