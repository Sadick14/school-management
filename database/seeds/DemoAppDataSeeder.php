<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\AppMeta;
use App\AcademicYear;
use App\Http\Helpers\AppHelper;
use App\IClass;
use App\Employee;
use App\Section;
use Illuminate\Support\Facades\DB;

class DemoAppDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //truncate previous data
        echo PHP_EOL, 'deleting old data.....';
        $this->deletePreviousData();

        //seed academic year
        echo PHP_EOL , 'seeding academic year...';
        $this->academicYearData();

        //seed common settings
        echo PHP_EOL , 'seeding institute settings...';
        $this->instituteSettingsData();

        //seed class
        echo PHP_EOL , 'seeding class...', PHP_EOL;
        $this->classData();
    }


    private function deletePreviousData(){
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("PRAGMA foreign_keys=OFF");
        } else {
            DB::statement("SET foreign_key_checks=0");
        }

        $this->deleteUserData();
        AcademicYear::truncate();
        AppMeta::truncate();
        IClass::truncate();
        Employee::truncate();
        Section::truncate();
        \App\Subject::truncate();
        \App\Student::truncate();
        \App\Registration::truncate();
        \App\StudentAttendance::truncate();
        \App\EmployeeAttendance::truncate();
        \App\Exam::truncate();
        \App\Grade::truncate();
        \App\ExamRule::truncate();
        \App\Mark::truncate();
        \App\Result::truncate();
        \App\Leave::truncate();
        \Illuminate\Support\Facades\DB::table('result_publish')->truncate();
        \Illuminate\Support\Facades\DB::table('teacher_subjects')->truncate();
        \Illuminate\Support\Facades\DB::table('student_subjects')->truncate();

        if (DB::getDriverName() === 'sqlite') {
            DB::statement("PRAGMA foreign_keys=ON");
        } else {
            DB::statement("SET foreign_key_checks=1");
        }

        //delete images
        $storagePath = storage_path('app/public');
        $storagePath2 = storage_path('app');
        $dirs = [
            $storagePath.'/admission',
            $storagePath.'/employee',
            $storagePath.'/invoice',
            $storagePath.'/leave',
            $storagePath.'/logo',
            $storagePath.'/report',
            $storagePath.'/student',
            $storagePath.'/work_outside',
            $storagePath2.'/student-attendance',
            $storagePath2.'/employee-attendance',
        ];

        foreach ($dirs as $dir){
            system("rm -rf ".escapeshellarg($dir));
        }
    }

    private function deleteUserData(){
        $userIds = \App\UserRole::where('role_id','!=', AppHelper::USER_ADMIN)->pluck('user_id');
        DB::table('users_permissions')->whereIn('user_id', $userIds)->delete();
        DB::table('user_roles')->whereIn('user_id', $userIds)->delete();
        DB::table('users')->whereIn('id', $userIds)->delete();

    }

    private function academicYearData(){
        $data['title'] = date('Y');
        $data['start_date'] = Carbon::createFromFormat('d/m/Y', '01/01/'.date('Y'));;
        $data['end_date'] = Carbon::createFromFormat('d/m/Y', '31/12/'.date('Y'));
        $data['status'] = '1';

        AcademicYear::create($data);
    }

    private function instituteSettingsData()
    {
        $originFilePath = resource_path('assets/backend/images/');
        $destinationPath = storage_path('app').'/public/logo/';

        if(!is_dir($destinationPath)) {
            mkdir($destinationPath);
        }

        $fileName = 'logo-md.png';
        copy($originFilePath.$fileName, $destinationPath.$fileName);
        $data['logo'] = $fileName;

        $fileName = 'logo-xs.png';
        copy($originFilePath.$fileName, $destinationPath.$fileName);
        $data['logo_small'] = $fileName;

        $fileName = 'favicon.png';
        copy($originFilePath.$fileName, $destinationPath.$fileName);
        $data['favicon'] = $fileName;


        $data['name'] = 'DevSuite Edu';
        $data['short_name'] = 'DSE';
        $data['establish'] = '2010';
        $data['website_link'] = 'http://devsuiteedu.com';
        $data['email'] = 'info@devsuiteedu.com';
        $data['phone_no'] = '+233302123456';
        $data['address'] = 'Accra, Ghana';

        $created_by = 1;
        $created_at = Carbon::now(env('APP_TIMEZONE','Africa/Accra'));

        //now create
        AppMeta::create([
            'meta_key' => 'institute_settings',
            'meta_value' => json_encode($data),
            'created_by' => $created_by,
            'created_at' => $created_at
        ]);

        if(AppHelper::getInstituteCategory() != 'college') {
            AppMeta::create([
                'meta_key' => 'academic_year',
                'meta_value' => 1,
                'created_by' => $created_by,
                'created_at' => $created_at
            ]);
        }

        $created_by = 1;
        $created_at = Carbon::now(env('APP_TIMEZONE','Africa/Accra'));
        $shiftData = [
            'Morning' => [
                'start' => '08:00 am',
                'end' => '01:00 pm',
            ],
            'Day' => [
                'start' => '02:00 pm',
                'end' => '07:00 pm',
            ],
            'Evening' => [
                'start' => '12:00 am',
                'end' => '12:00 am',
            ]
        ];
        $insertData = [
            ['meta_key' => 'frontend_website' ,'meta_value' => 1, 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'language', 'meta_value' =>  'en', 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'disable_language', 'meta_value' => 1, 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'institute_type', 'meta_value' => 1, 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'shift_data', 'meta_value' => json_encode($shiftData), 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'weekends', 'meta_value' => json_encode([5]), 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'week_start_day', 'meta_value' => 6, 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'week_end_day', 'meta_value' => 5, 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'total_casual_leave', 'meta_value' => 14, 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'total_sick_leave', 'meta_value' => 10, 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'total_maternity_leave', 'meta_value' => 90, 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'total_special_leave', 'meta_value' => 5, 'created_by' => $created_by, 'created_at' => $created_at],
            ['meta_key' => 'board_name', 'meta_value' => 'Ghana Education Service', 'created_by' => $created_by, 'created_at' => $created_at]
        ];

        //now crate
        AppMeta::insert($insertData);

        //invalid previous cache
        \Illuminate\Support\Facades\Cache::forget('app_settings');
    }



    private function classData(){
        $created_by = 1;
        $created_at = Carbon::now(env('APP_TIMEZONE','Africa/Accra'));

        $insertData = [
            //explicit ids keep class_id references stable for downstream demo data
            //(subjects, sections, exams, marks/results); SHS classes are not seeded
            [
                'id' => 1,
                'name' => 'JHS 1',
                'numeric_value' => 11,
                'order' => 11,
                'group' => 'None',
                'status' => '1',
                'note' => 'Junior High School 1',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 2,
                'name' => 'JHS 2',
                'numeric_value' => 12,
                'order' => 12,
                'group' => 'None',
                'status' => '1',
                'note' => 'Junior High School 2',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 3,
                'name' => 'JHS 3',
                'numeric_value' => 13,
                'order' => 13,
                'group' => 'None',
                'status' => '1',
                'note' => 'Junior High School 3',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],

            //pre-tertiary levels (Nursery, Kindergarten, Primary) - explicit ids 11-20;
            //'order' controls their position at the top of the class list
            [
                'id' => 11,
                'name' => 'Nursery 1',
                'numeric_value' => 1,
                'order' => 1,
                'group' => 'None',
                'status' => '1',
                'note' => 'Nursery 1',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 12,
                'name' => 'Nursery 2',
                'numeric_value' => 2,
                'order' => 2,
                'group' => 'None',
                'status' => '1',
                'note' => 'Nursery 2',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 13,
                'name' => 'KG 1',
                'numeric_value' => 3,
                'order' => 3,
                'group' => 'None',
                'status' => '1',
                'note' => 'Kindergarten 1',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 14,
                'name' => 'KG 2',
                'numeric_value' => 4,
                'order' => 4,
                'group' => 'None',
                'status' => '1',
                'note' => 'Kindergarten 2',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 15,
                'name' => 'Class 1',
                'numeric_value' => 5,
                'order' => 5,
                'group' => 'None',
                'status' => '1',
                'note' => 'Primary Class 1',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 16,
                'name' => 'Class 2',
                'numeric_value' => 6,
                'order' => 6,
                'group' => 'None',
                'status' => '1',
                'note' => 'Primary Class 2',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 17,
                'name' => 'Class 3',
                'numeric_value' => 7,
                'order' => 7,
                'group' => 'None',
                'status' => '1',
                'note' => 'Primary Class 3',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 18,
                'name' => 'Class 4',
                'numeric_value' => 8,
                'order' => 8,
                'group' => 'None',
                'status' => '1',
                'note' => 'Primary Class 4',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 19,
                'name' => 'Class 5',
                'numeric_value' => 9,
                'order' => 9,
                'group' => 'None',
                'status' => '1',
                'note' => 'Primary Class 5',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
            [
                'id' => 20,
                'name' => 'Class 6',
                'numeric_value' => 10,
                'order' => 10,
                'group' => 'None',
                'status' => '1',
                'note' => 'Primary Class 6',
                'created_by' => $created_by,
                'created_at' => $created_at
            ],
        ];

        IClass::insert($insertData);
    }

}
