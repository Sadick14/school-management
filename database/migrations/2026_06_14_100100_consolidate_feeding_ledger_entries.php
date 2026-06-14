<?php

use App\Http\Helpers\AppHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ConsolidateFeedingLedgerEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $feedingFeeTypeId = DB::table('fee_types')->where('code', 'FEEDING')->value('id');

        if (!$feedingFeeTypeId) {
            return;
        }

        $rows = DB::table('student_ledgers')
            ->where('fee_type_id', $feedingFeeTypeId)
            ->whereNotNull('billing_date')
            ->where('status', AppHelper::ACTIVE)
            ->get()
            ->groupBy('registration_id');

        foreach ($rows as $registrationId => $ledgers) {
            $first = $ledgers->first();
            $totalAmount = $ledgers->sum('amount');
            $totalPaid = $ledgers->sum('amount_paid');

            $walletId = DB::table('student_ledgers')->insertGetId([
                'registration_id' => $registrationId,
                'student_id' => $first->student_id,
                'academic_year_id' => $first->academic_year_id,
                'fee_type_id' => $feedingFeeTypeId,
                'term_id' => null,
                'billing_date' => null,
                'description' => 'Feeding Fee',
                'amount' => $totalAmount,
                'amount_paid' => $totalPaid,
                'balance' => bcsub((string) $totalAmount, (string) $totalPaid, 2),
                'source' => 'auto',
                'status' => AppHelper::ACTIVE,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($ledgers as $ledger) {
                DB::table('feeding_charges')->insert([
                    'registration_id' => $registrationId,
                    'student_ledger_id' => $walletId,
                    'charge_date' => $ledger->billing_date,
                    'amount' => $ledger->amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('student_ledgers')
                ->whereIn('id', $ledgers->pluck('id'))
                ->update(['status' => AppHelper::INACTIVE, 'updated_at' => now()]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $feedingFeeTypeId = DB::table('fee_types')->where('code', 'FEEDING')->value('id');

        if (!$feedingFeeTypeId) {
            return;
        }

        DB::table('student_ledgers')
            ->where('fee_type_id', $feedingFeeTypeId)
            ->whereNotNull('billing_date')
            ->where('status', AppHelper::INACTIVE)
            ->update(['status' => AppHelper::ACTIVE, 'updated_at' => now()]);

        $walletIds = DB::table('student_ledgers')
            ->where('fee_type_id', $feedingFeeTypeId)
            ->whereNull('term_id')
            ->whereNull('billing_date')
            ->pluck('id');

        DB::table('feeding_charges')->whereIn('student_ledger_id', $walletIds)->delete();
        DB::table('student_ledgers')->whereIn('id', $walletIds)->delete();
    }
}
