<?php

use Illuminate\Database\Seeder;
use App\FeeType;
use App\ExpenseCategory;
use App\Http\Helpers\AppHelper;

class FinanceDataSeeder extends Seeder
{
    public function run()
    {
        echo PHP_EOL, 'seeding fee types...', PHP_EOL;

        $feeTypes = [
            ['code' => 'SCHOOL', 'name' => 'School Fees', 'billing_cycle' => 'term', 'applies_to' => 'all', 'is_optional' => false],
            ['code' => 'FEEDING', 'name' => 'Feeding Fee', 'billing_cycle' => 'daily', 'applies_to' => 'all', 'is_optional' => false],
            ['code' => 'REGISTRATION', 'name' => 'Registration Fee', 'billing_cycle' => 'once_per_student', 'applies_to' => 'new_students_only', 'is_optional' => false],
            ['code' => 'PTA', 'name' => 'PTA Levy', 'billing_cycle' => 'ad_hoc', 'applies_to' => 'all', 'is_optional' => true],
            ['code' => 'SPORT', 'name' => 'Sports Fee', 'billing_cycle' => 'ad_hoc', 'applies_to' => 'all', 'is_optional' => true],
            ['code' => 'BOOKS', 'name' => 'Books Fee', 'billing_cycle' => 'ad_hoc', 'applies_to' => 'all', 'is_optional' => true],
            ['code' => 'GRADUATION', 'name' => 'Graduation Fee', 'billing_cycle' => 'ad_hoc', 'applies_to' => 'all', 'is_optional' => true],
        ];

        foreach ($feeTypes as $type) {
            FeeType::updateOrCreate(
                ['code' => $type['code']],
                array_merge($type, ['status' => AppHelper::ACTIVE])
            );
        }

        echo PHP_EOL, 'seeding expense categories...', PHP_EOL;

        $categories = [
            'Salaries & Wages',
            'Utilities',
            'Office Supplies',
            'Maintenance',
            'Transport',
            'Food & Catering',
            'Events',
            'Miscellaneous',
        ];

        foreach ($categories as $name) {
            ExpenseCategory::updateOrCreate(
                ['name' => $name],
                ['status' => AppHelper::ACTIVE]
            );
        }
    }
}
