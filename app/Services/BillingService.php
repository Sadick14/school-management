<?php

namespace App\Services;

use App\AcademicTerm;
use App\FeedingCharge;
use App\FeePayment;
use App\FeeStructure;
use App\FeeType;
use App\Http\Helpers\AppHelper;
use App\Registration;
use App\StudentAttendance;
use App\StudentLedger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Generate term-based school fees for registrations in an academic year.
     */
    public function generateTermFees($academicYearId, $termId, $classId = null)
    {
        $term = AcademicTerm::where('id', $termId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', AppHelper::ACTIVE)
            ->first();

        if (!$term) {
            return 0;
        }

        $schoolFeeType = FeeType::where('code', 'SCHOOL')->first();
        if (!$schoolFeeType) {
            return 0;
        }

        $structures = FeeStructure::where('academic_year_id', $academicYearId)
            ->where('fee_type_id', $schoolFeeType->id)
            ->where(function ($q) use ($termId) {
                $q->where('term_id', $termId)->orWhereNull('term_id');
            })
            ->where('status', AppHelper::ACTIVE)
            ->get();

        if ($structures->isEmpty()) {
            return 0;
        }

        $registrations = Registration::where('academic_year_id', $academicYearId)
            ->where('status', AppHelper::ACTIVE)
            ->when($classId, function ($q) use ($classId) {
                $q->where('class_id', $classId);
            })
            ->get();

        $created = 0;
        foreach ($registrations as $registration) {
            $structure = $this->resolveFeeStructure($structures, $registration->class_id, $termId);
            if (!$structure) {
                continue;
            }

            if (!$this->feeAppliesToRegistration($schoolFeeType, $registration)) {
                continue;
            }

            $exists = StudentLedger::where('registration_id', $registration->id)
                ->where('fee_type_id', $schoolFeeType->id)
                ->where('term_id', $termId)
                ->where('source', '!=', 'opening_balance')
                ->count();

            if ($exists) {
                continue;
            }

            $this->createLedgerEntry([
                'registration_id' => $registration->id,
                'student_id' => $registration->student_id,
                'academic_year_id' => $academicYearId,
                'fee_type_id' => $schoolFeeType->id,
                'term_id' => $termId,
                'description' => $schoolFeeType->name . ' - ' . $term->name,
                'amount' => $structure->amount,
                'source' => 'auto',
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * Generate registration fee for a student on first registration only.
     */
    public function generateRegistrationFee($registrationId)
    {
        $registration = Registration::find($registrationId);
        if (!$registration) {
            return null;
        }

        $feeType = FeeType::where('code', 'REGISTRATION')->first();
        if (!$feeType) {
            return null;
        }

        $priorCount = Registration::where('student_id', $registration->student_id)
            ->where('id', '!=', $registration->id)
            ->count();

        if ($priorCount > 0) {
            return null;
        }

        $exists = StudentLedger::where('registration_id', $registration->id)
            ->where('fee_type_id', $feeType->id)
            ->count();

        if ($exists) {
            return StudentLedger::where('registration_id', $registration->id)
                ->where('fee_type_id', $feeType->id)
                ->first();
        }

        $structure = $this->getStructureForRegistration($registration, $feeType->id);
        if (!$structure) {
            return null;
        }

        return $this->createLedgerEntry([
            'registration_id' => $registration->id,
            'student_id' => $registration->student_id,
            'academic_year_id' => $registration->academic_year_id,
            'fee_type_id' => $feeType->id,
            'description' => $feeType->name,
            'amount' => $structure->amount,
            'source' => 'auto',
        ]);
    }

    /**
     * Generate weekday feeding charges between two dates (Mon-Fri only), accruing
     * them onto a single per-registration "wallet" ledger row. A day only adds to
     * the wallet if the student was fed (per attendance, defaulting to fed when
     * no attendance record exists). Never charges for future dates.
     */
    public function generateFeedingFees($registrationId, $dateFrom, $dateTo)
    {
        $registration = Registration::find($registrationId);
        if (!$registration) {
            return 0;
        }

        $feeType = FeeType::where('code', 'FEEDING')->first();
        if (!$feeType) {
            return 0;
        }

        $structure = $this->getStructureForRegistration($registration, $feeType->id);
        if (!$structure) {
            return 0;
        }

        $wallet = StudentLedger::where('registration_id', $registration->id)
            ->where('fee_type_id', $feeType->id)
            ->whereNull('term_id')
            ->whereNull('billing_date')
            ->first();

        if (!$wallet) {
            $wallet = $this->createLedgerEntry([
                'registration_id' => $registration->id,
                'student_id' => $registration->student_id,
                'academic_year_id' => $registration->academic_year_id,
                'fee_type_id' => $feeType->id,
                'description' => $feeType->name,
                'amount' => 0,
                'source' => 'auto',
            ]);
        }

        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->startOfDay();
        $today = Carbon::now()->startOfDay();

        if ($end->gt($today)) {
            $end = $today->copy();
        }

        $charged = 0;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            $dateStr = $date->format('Y-m-d');

            $exists = FeedingCharge::where('registration_id', $registration->id)
                ->whereDate('charge_date', $dateStr)
                ->exists();

            if ($exists) {
                continue;
            }

            $attendance = StudentAttendance::where('registration_id', $registration->id)
                ->whereDate('attendance_date', $dateStr)
                ->first();

            $fed = $attendance ? ($attendance->present === AppHelper::ATTENDANCE_TYPE[1]) : true;
            $amount = 0;

            if ($fed) {
                $wallet->amount = bcadd($wallet->amount, $structure->amount, 2);
                $wallet->balance = bcsub($wallet->amount, $wallet->amount_paid, 2);
                $wallet->save();

                $amount = $structure->amount;
                $charged++;
            }

            FeedingCharge::create([
                'registration_id' => $registration->id,
                'student_ledger_id' => $wallet->id,
                'charge_date' => $dateStr,
                'amount' => $amount,
            ]);
        }

        return $charged;
    }

    /**
     * Carry forward arrears and credit balances to a new registration after promotion.
     */
    public function carryForwardBalances($oldRegistrationId, $newRegistrationId)
    {
        $oldRegistration = Registration::find($oldRegistrationId);
        $newRegistration = Registration::find($newRegistrationId);

        if (!$oldRegistration || !$newRegistration) {
            return 0;
        }

        $openLedgers = StudentLedger::with('feeType')
            ->where('registration_id', $oldRegistrationId)
            ->where('status', AppHelper::ACTIVE)
            ->where('balance', '!=', 0)
            ->get();

        $carried = 0;
        foreach ($openLedgers as $ledger) {
            $exists = StudentLedger::where('registration_id', $newRegistrationId)
                ->where('source', 'opening_balance')
                ->where('description', 'like', 'Carried forward:%')
                ->where('fee_type_id', $ledger->fee_type_id)
                ->when($ledger->term_id, function ($q) use ($ledger) {
                    $q->where('term_id', $ledger->term_id);
                })
                ->when($ledger->billing_date, function ($q) use ($ledger) {
                    $q->whereDate('billing_date', $ledger->billing_date);
                })
                ->count();

            if ($exists) {
                continue;
            }

            $label = $ledger->feeType ? $ledger->feeType->name : 'Balance';
            $this->createLedgerEntry([
                'registration_id' => $newRegistration->id,
                'student_id' => $newRegistration->student_id,
                'academic_year_id' => $newRegistration->academic_year_id,
                'fee_type_id' => $ledger->fee_type_id,
                'term_id' => $ledger->term_id,
                'billing_date' => $ledger->billing_date,
                'description' => 'Carried forward: ' . $label . ($ledger->description ? ' (' . $ledger->description . ')' : ''),
                'amount' => $ledger->balance,
                'source' => 'opening_balance',
            ]);
            $carried++;
        }

        return $carried;
    }

    /**
     * Create an ad-hoc or optional fee ledger entry from fee structure.
     */
    public function createOptionalFeeLedger($registrationId, $feeTypeId)
    {
        $registration = Registration::find($registrationId);
        $feeType = FeeType::find($feeTypeId);

        if (!$registration || !$feeType) {
            return null;
        }

        if (!$this->feeAppliesToRegistration($feeType, $registration)) {
            return null;
        }

        $structure = $this->getStructureForRegistration($registration, $feeTypeId);
        if (!$structure) {
            return null;
        }

        $exists = StudentLedger::where('registration_id', $registrationId)
            ->where('fee_type_id', $feeTypeId)
            ->where('academic_year_id', $registration->academic_year_id)
            ->where('source', 'manual')
            ->count();

        if ($exists && $feeType->billing_cycle === 'ad_hoc') {
            return null;
        }

        return $this->createLedgerEntry([
            'registration_id' => $registration->id,
            'student_id' => $registration->student_id,
            'academic_year_id' => $registration->academic_year_id,
            'fee_type_id' => $feeTypeId,
            'description' => $feeType->name,
            'amount' => $structure->amount,
            'source' => 'manual',
        ]);
    }

    /**
     * Ensure billing is generated and return open ledger items for registrations.
     */
    public function prepareDuesForRegistrations(array $registrationIds, $feedingFrom = null, $feedingTo = null)
    {
        $registrations = Registration::whereIn('id', $registrationIds)->get();
        $feedingFrom = $feedingFrom ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $feedingTo = $feedingTo ?: Carbon::now()->format('Y-m-d');

        $activeTerm = null;
        $feedingFeeTypeId = FeeType::where('code', 'FEEDING')->value('id');

        foreach ($registrations as $registration) {
            $activeTerm = AppHelper::getActiveTerm($registration->academic_year_id);

            if ($activeTerm) {
                $this->generateTermFees($registration->academic_year_id, $activeTerm->id, $registration->class_id);
            }

            $this->generateRegistrationFee($registration->id);
            $this->generateFeedingFees($registration->id, $feedingFrom, $feedingTo);
        }

        return StudentLedger::with(['feeType', 'term', 'registration.student', 'registration.class'])
            ->whereIn('registration_id', $registrationIds)
            ->where('status', AppHelper::ACTIVE)
            ->where(function ($q) use ($feedingFeeTypeId) {
                $q->where('balance', '!=', 0)
                    ->when($feedingFeeTypeId, function ($q2) use ($feedingFeeTypeId) {
                        $q2->orWhere('fee_type_id', $feedingFeeTypeId);
                    });
            })
            ->where(function ($q) use ($activeTerm) {
                $q->whereNull('term_id')
                    ->when($activeTerm, function ($q2) use ($activeTerm) {
                        $q2->orWhere('term_id', $activeTerm->id);
                    });
            })
            ->orderBy('registration_id')
            ->orderBy('billing_date')
            ->get();
    }

    /**
     * Apply a payment to ledger items.
     */
    public function applyPayment(array $paymentData, array $items)
    {
        $feedingFeeTypeId = FeeType::where('code', 'FEEDING')->value('id');

        return DB::transaction(function () use ($paymentData, $items, $feedingFeeTypeId) {
            $payment = FeePayment::create(array_merge($paymentData, [
                'receipt_no' => $this->generateReceiptNo(),
            ]));

            foreach ($items as $item) {
                $ledger = StudentLedger::lockForUpdate()->findOrFail($item['ledger_id']);
                $amountApplied = (float) $item['amount'];

                if ($amountApplied <= 0) {
                    continue;
                }

                $isFeedingWallet = $feedingFeeTypeId
                    && $ledger->fee_type_id == $feedingFeeTypeId
                    && is_null($ledger->term_id)
                    && is_null($ledger->billing_date);

                if (!$isFeedingWallet) {
                    if ($ledger->balance > 0 && $amountApplied > $ledger->balance) {
                        throw new \RuntimeException('Payment exceeds balance for ' . ($ledger->description ?: 'ledger item'));
                    }

                    if ($ledger->balance < 0 && $amountApplied > abs($ledger->balance)) {
                        throw new \RuntimeException('Credit application exceeds available credit.');
                    }
                }

                $ledger->amount_paid = bcadd($ledger->amount_paid, $amountApplied, 2);
                $ledger->balance = bcsub($ledger->amount, $ledger->amount_paid, 2);
                $ledger->save();

                $payment->items()->create([
                    'student_ledger_id' => $ledger->id,
                    'amount_applied' => $amountApplied,
                ]);
            }

            return $payment->load(['items.ledger.feeType', 'student', 'registration.class']);
        });
    }

    public function generateReceiptNo()
    {
        $prefix = 'RCP-' . date('Ymd') . '-';
        $last = FeePayment::where('receipt_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->value('receipt_no');

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last);
            $seq = (int) end($parts) + 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    protected function createLedgerEntry(array $data)
    {
        $amount = (float) ($data['amount'] ?? 0);
        $data['amount_paid'] = $data['amount_paid'] ?? 0;
        $data['balance'] = bcsub($amount, $data['amount_paid'], 2);
        $data['status'] = AppHelper::ACTIVE;

        return StudentLedger::create($data);
    }

    protected function getStructureForRegistration(Registration $registration, $feeTypeId)
    {
        $structures = FeeStructure::where('academic_year_id', $registration->academic_year_id)
            ->where('fee_type_id', $feeTypeId)
            ->where('status', AppHelper::ACTIVE)
            ->get();

        return $this->resolveFeeStructure($structures, $registration->class_id, null);
    }

    protected function resolveFeeStructure($structures, $classId, $termId)
    {
        $match = $structures->first(function ($s) use ($classId, $termId) {
            return $s->class_id == $classId && ($termId ? $s->term_id == $termId : true);
        });

        if ($match) {
            return $match;
        }

        $match = $structures->first(function ($s) use ($classId) {
            return $s->class_id == $classId && is_null($s->term_id);
        });

        if ($match) {
            return $match;
        }

        $match = $structures->first(function ($s) use ($termId) {
            return is_null($s->class_id) && $termId && $s->term_id == $termId;
        });

        if ($match) {
            return $match;
        }

        return $structures->first(function ($s) {
            return is_null($s->class_id) && is_null($s->term_id);
        });
    }

    protected function feeAppliesToRegistration(FeeType $feeType, Registration $registration)
    {
        if ($feeType->applies_to === 'all') {
            return true;
        }

        $isNew = Registration::where('student_id', $registration->student_id)
            ->where('id', '!=', $registration->id)
            ->count() === 0;

        if ($feeType->applies_to === 'new_students_only') {
            return $isNew;
        }

        if ($feeType->applies_to === 'continuing_only') {
            return !$isNew;
        }

        return true;
    }
}
