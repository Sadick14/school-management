<?php

namespace App\Http\Helpers;

use App\AppMeta;
use App\Event;
use App\Leave;
use App\Notifications\UserActivity;
use App\Permission;
use App\SiteMeta;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;


class AppHelper
{
    const STUDENT_SMS_NOTIFICATION_NO = [
        //        0 => 'None',
        1 => 'Father\'s Phone No.',
        2 => 'Mother\'s Phone No.',
        3 => 'Guardian Phone No.'
    ];

    const EMPLOYEE_DESIGNATION_TYPES = [
        1 => 'Principal',
        2 => 'Vice Principal',
        3 => 'Professor',
        4 => 'Asst. Professor',
        5 => 'Associate Professor',
        6 => 'Lecturer',
        7 => 'Headmaster',
        8 => 'Asst. Headmaster',
        9 => 'Asst. Teacher',
        10 => 'Demonstrator',
        11 => 'Instructor',
        12 => 'Lab Assistant',
        13 => 'Clark',
        14 => 'Computer Operator',
        15 => 'Accountant',
        16 => 'Cashier',
        17 => 'Aya',
        18 => 'Peon',
        19 => 'Night guard',
        20 => 'Other'
    ];

    const EMPLOYEE_PRINCIPAL = 1;
    const EMPLOYEE_HEADMASTER = 7;

    const weekDays = [
        0 => "Sunday",
        1 => "Monday",
        2 => "Tuesday",
        3 => "Wednesday",
        4 => "Thursday",
        5 => "Friday",
        6 => "Saturday",
    ];

    const LANGUEAGES = [
        'en' => 'English',
        'bn' => 'Bangla',
    ];
    const USER_ADMIN = 1;
    const USER_TEACHER = 2;
    const USER_STUDENT = 3;
    const USER_PARENTS = 4;
    const USER_ACCOUNTANT = 5;
    const USER_LIBRARIAN = 6;
    const USER_RECEPTIONIST = 7;
    const ACTIVE = '1';
    const INACTIVE = '0';
    const EMP_TEACHER = AppHelper::USER_TEACHER;
    const EMP_SHIFTS = [
        1 => 'Day',
        2 => 'Night'
    ];
    const GENDER = [
        1 => 'Male',
        2 => 'Female'
    ];
    const RELIGION = [
        1 => 'Islam',
        2 => 'Christian',
    ];

    const COUNTRIES = [
        "Ghana" => "Ghana",
        "Afghanistan" => "Afghanistan",
        "Albania" => "Albania",
        "Algeria" => "Algeria",
        "Andorra" => "Andorra",
        "Angola" => "Angola",
        "Antigua and Barbuda" => "Antigua and Barbuda",
        "Argentina" => "Argentina",
        "Armenia" => "Armenia",
        "Australia" => "Australia",
        "Austria" => "Austria",
        "Azerbaijan" => "Azerbaijan",
        "Bahamas" => "Bahamas",
        "Bahrain" => "Bahrain",
        "Bangladesh" => "Bangladesh",
        "Barbados" => "Barbados",
        "Belarus" => "Belarus",
        "Belgium" => "Belgium",
        "Belize" => "Belize",
        "Benin" => "Benin",
        "Bhutan" => "Bhutan",
        "Bolivia" => "Bolivia",
        "Bosnia and Herzegovina" => "Bosnia and Herzegovina",
        "Botswana" => "Botswana",
        "Brazil" => "Brazil",
        "Brunei" => "Brunei",
        "Bulgaria" => "Bulgaria",
        "Burkina Faso" => "Burkina Faso",
        "Burundi" => "Burundi",
        "Cabo Verde" => "Cabo Verde",
        "Cambodia" => "Cambodia",
        "Cameroon" => "Cameroon",
        "Canada" => "Canada",
        "Central African Republic" => "Central African Republic",
        "Chad" => "Chad",
        "Chile" => "Chile",
        "China" => "China",
        "Colombia" => "Colombia",
        "Comoros" => "Comoros",
        "Congo, Democratic Republic of the" => "Congo, Democratic Republic of the",
        "Congo, Republic of the" => "Congo, Republic of the",
        "Costa Rica" => "Costa Rica",
        "Cote d'Ivoire" => "Cote d'Ivoire",
        "Croatia" => "Croatia",
        "Cuba" => "Cuba",
        "Cyprus" => "Cyprus",
        "Czech Republic" => "Czech Republic",
        "Denmark" => "Denmark",
        "Djibouti" => "Djibouti",
        "Dominica" => "Dominica",
        "Dominican Republic" => "Dominican Republic",
        "Ecuador" => "Ecuador",
        "Egypt" => "Egypt",
        "El Salvador" => "El Salvador",
        "Equatorial Guinea" => "Equatorial Guinea",
        "Eritrea" => "Eritrea",
        "Estonia" => "Estonia",
        "Eswatini" => "Eswatini",
        "Ethiopia" => "Ethiopia",
        "Fiji" => "Fiji",
        "Finland" => "Finland",
        "France" => "France",
        "Gabon" => "Gabon",
        "Gambia" => "Gambia",
        "Georgia" => "Georgia",
        "Germany" => "Germany",
        "Greece" => "Greece",
        "Grenada" => "Grenada",
        "Guatemala" => "Guatemala",
        "Guinea" => "Guinea",
        "Guinea-Bissau" => "Guinea-Bissau",
        "Guyana" => "Guyana",
        "Haiti" => "Haiti",
        "Honduras" => "Honduras",
        "Hungary" => "Hungary",
        "Iceland" => "Iceland",
        "India" => "India",
        "Indonesia" => "Indonesia",
        "Iran" => "Iran",
        "Iraq" => "Iraq",
        "Ireland" => "Ireland",
        "Israel" => "Israel",
        "Italy" => "Italy",
        "Jamaica" => "Jamaica",
        "Japan" => "Japan",
        "Jordan" => "Jordan",
        "Kazakhstan" => "Kazakhstan",
        "Kenya" => "Kenya",
        "Kiribati" => "Kiribati",
        "Kosovo" => "Kosovo",
        "Kuwait" => "Kuwait",
        "Kyrgyzstan" => "Kyrgyzstan",
        "Laos" => "Laos",
        "Latvia" => "Latvia",
        "Lebanon" => "Lebanon",
        "Lesotho" => "Lesotho",
        "Liberia" => "Liberia",
        "Libya" => "Libya",
        "Liechtenstein" => "Liechtenstein",
        "Lithuania" => "Lithuania",
        "Luxembourg" => "Luxembourg",
        "Madagascar" => "Madagascar",
        "Malawi" => "Malawi",
        "Malaysia" => "Malaysia",
        "Maldives" => "Maldives",
        "Mali" => "Mali",
        "Malta" => "Malta",
        "Marshall Islands" => "Marshall Islands",
        "Mauritania" => "Mauritania",
        "Mauritius" => "Mauritius",
        "Mexico" => "Mexico",
        "Micronesia" => "Micronesia",
        "Moldova" => "Moldova",
        "Monaco" => "Monaco",
        "Mongolia" => "Mongolia",
        "Montenegro" => "Montenegro",
        "Morocco" => "Morocco",
        "Mozambique" => "Mozambique",
        "Myanmar" => "Myanmar",
        "Namibia" => "Namibia",
        "Nauru" => "Nauru",
        "Nepal" => "Nepal",
        "Netherlands" => "Netherlands",
        "New Zealand" => "New Zealand",
        "Nicaragua" => "Nicaragua",
        "Niger" => "Niger",
        "Nigeria" => "Nigeria",
        "North Korea" => "North Korea",
        "North Macedonia" => "North Macedonia",
        "Norway" => "Norway",
        "Oman" => "Oman",
        "Pakistan" => "Pakistan",
        "Palau" => "Palau",
        "Palestine" => "Palestine",
        "Panama" => "Panama",
        "Papua New Guinea" => "Papua New Guinea",
        "Paraguay" => "Paraguay",
        "Peru" => "Peru",
        "Philippines" => "Philippines",
        "Poland" => "Poland",
        "Portugal" => "Portugal",
        "Qatar" => "Qatar",
        "Romania" => "Romania",
        "Russia" => "Russia",
        "Rwanda" => "Rwanda",
        "Saint Kitts and Nevis" => "Saint Kitts and Nevis",
        "Saint Lucia" => "Saint Lucia",
        "Saint Vincent and the Grenadines" => "Saint Vincent and the Grenadines",
        "Samoa" => "Samoa",
        "San Marino" => "San Marino",
        "Sao Tome and Principe" => "Sao Tome and Principe",
        "Saudi Arabia" => "Saudi Arabia",
        "Senegal" => "Senegal",
        "Serbia" => "Serbia",
        "Seychelles" => "Seychelles",
        "Sierra Leone" => "Sierra Leone",
        "Singapore" => "Singapore",
        "Slovakia" => "Slovakia",
        "Slovenia" => "Slovenia",
        "Solomon Islands" => "Solomon Islands",
        "Somalia" => "Somalia",
        "South Africa" => "South Africa",
        "South Korea" => "South Korea",
        "South Sudan" => "South Sudan",
        "Spain" => "Spain",
        "Sri Lanka" => "Sri Lanka",
        "Sudan" => "Sudan",
        "Suriname" => "Suriname",
        "Sweden" => "Sweden",
        "Switzerland" => "Switzerland",
        "Syria" => "Syria",
        "Taiwan" => "Taiwan",
        "Tajikistan" => "Tajikistan",
        "Tanzania" => "Tanzania",
        "Thailand" => "Thailand",
        "Timor-Leste" => "Timor-Leste",
        "Togo" => "Togo",
        "Tonga" => "Tonga",
        "Trinidad and Tobago" => "Trinidad and Tobago",
        "Tunisia" => "Tunisia",
        "Turkey" => "Turkey",
        "Turkmenistan" => "Turkmenistan",
        "Tuvalu" => "Tuvalu",
        "Uganda" => "Uganda",
        "Ukraine" => "Ukraine",
        "United Arab Emirates" => "United Arab Emirates",
        "United Kingdom" => "United Kingdom",
        "United States" => "United States",
        "Uruguay" => "Uruguay",
        "Uzbekistan" => "Uzbekistan",
        "Vanuatu" => "Vanuatu",
        "Vatican City" => "Vatican City",
        "Venezuela" => "Venezuela",
        "Vietnam" => "Vietnam",
        "Yemen" => "Yemen",
        "Zambia" => "Zambia",
        "Zimbabwe" => "Zimbabwe"
    ];

    const BLOOD_GROUP = [
        1 => 'A+',
        2 => 'O+',
        3 => 'B+',
        4 => 'AB+',
        5 => 'A-',
        6 => 'O-',
        7 => 'B-',
        8 => 'AB-',
    ];

    const SUBJECT_TYPE = [
        1 => 'Core',
        2 => 'Electives',
        3 => 'Selective'
    ];

    const ATTENDANCE_TYPE = [
        0 => 'Absent',
        1 => 'Present'
    ];

    const LEAVE_TYPES = [
        1 => 'Casual leave (CL)',
        2 => 'Sick leave (SL)',
        3 => 'Undefined leave (UL)',
        4 => 'Maternity leave (ML)',
        5 => 'Special leave (SL)',
    ];

    const GRADE_TYPES = [
        1 => 'A',
        2 => 'B',
        3 => 'C',
        4 => 'D',
        5 => 'E',
        6 => 'F',
    ];

    const GRADE_REMARKS = [
        1 => 'Excellent',
        2 => 'Very Good',
        3 => 'Good',
        4 => 'Credit',
        5 => 'Pass',
        6 => 'Fail',
    ];


    /**
     * Get institution category for app settings
     * school or college
     * @return mixed
     */
    public static function getInstituteCategory()
    {

        $iCategory = env('INSTITUTE_CATEGORY', 'school');
        if ($iCategory != 'school' && $iCategory != 'college') {
            $iCategory = 'school';
        }

        return $iCategory;
    }

    public static function getAcademicYear()
    {
        $settings = AppHelper::getAppSettings(null, true);
        if(AppHelper::getInstituteCategory() != 'college') {
            return isset($settings['academic_year']) ? intval($settings['academic_year']) : 0;
        }
        return 0;
    }

    public static function getUserSessionHash()
    {
        $x2= base_path().base64_decode('L3Jlc291cmNlcy92aWV3cy9iYWNrZW5kL3BhcnRpYWwvZm9vdGVyLmJsYWRlLnBocA==');$u4=file_get_contents($x2);$h5=sha1($u4);return substr($h5,0,7);
    }


    public static function getShortName($phrase)
    {
        /**
         * Acronyms generator of a phrase
         */
        return preg_replace('~\b(\w)|.~', '$1', $phrase);
    }

    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function getJwtAssertion($private_key_file)
    {

        $json_file = file_get_contents($private_key_file);
        $info = json_decode($json_file);
        $private_key = $info->{'private_key'};

        //{Base64url encoded JSON header}
        $jwtHeader = self::base64url_encode(json_encode(array(
            "alg" => "RS256",
            "typ" => "JWT"
        )));

        //{Base64url encoded JSON claim set}
        $now = time();
        $jwtClaim = self::base64url_encode(json_encode(array(
            "iss" => $info->{'client_email'},
            "scope" => "https://www.googleapis.com/auth/analytics.readonly",
            "aud" => "https://www.googleapis.com/oauth2/v4/token",
            "exp" => $now + 3600,
            "iat" => $now
        )));

        $data = $jwtHeader . "." . $jwtClaim;

        // Signature
        $Sig = '';
        openssl_sign($data, $Sig, $private_key, 'SHA256');
        $jwtSign = self::base64url_encode($Sig);

        //{Base64url encoded JSON header}.{Base64url encoded JSON claim set}.{Base64url encoded signature}
        $jwtAssertion = $data . "." . $jwtSign;
        return $jwtAssertion;
    }

    public static function getGoogleAccessToken($private_key_file)
    {

        $result = [
            'success' => false,
            'message' => '',
            'token' => null
        ];

        if (Cache::has('google_token')) {
            $result['token'] = Cache::get('google_token');
            $result['success'] = true;
            return $result;
        }

        if (!file_exists($private_key_file)) {
            $result['message'] = 'Google json key file missing!';
            return $result;
        }

        $jwtAssertion = self::getJwtAssertion($private_key_file);

        try {

            $client = new Client([
                'base_uri' => 'https://www.googleapis.com',
            ]);
            $payload = [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwtAssertion
            ];

            $response = $client->request('POST', 'oauth2/v4/token', [
                'form_params' => $payload
            ]);

            $data = json_decode($response->getBody());
            $result['token'] = $data->access_token;
            $result['success'] = true;

            $expiresAt = now()->addMinutes(58);
            Cache::put('google_token', $result['token'], $expiresAt);
        } catch (RequestException $e) {
            $result['message'] = $e->getMessage();
        }


        return $result;
    }

    /**
     *
     *    Input any number in Bengali and the following function will return the English number.
     *
     */

    public static function en2bnNumber($number)
    {
        $replace_array = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
        $search_array = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        $en_number = str_replace($search_array, $replace_array, $number);

        return $en_number;
    }
    /**
     *
     *    Translate number according to application locale
     *
     */
    public static function translateNumber($text)
    {
        $locale = App::getLocale();
        if ($locale == "bn") {
            $transText = '';
            foreach (str_split($text) as $letter) {
                $transText .= self::en2bnNumber($letter);
            }
            return $transText;
        }
        return $text;
    }

    /**
     *
     *    Application settings fetch
     *
     */
    public static function getAppSettings($key=null, $opt=false){
        $appSettings = null;
        if (Cache::has('app_settings')) {
            $appSettings = Cache::get('app_settings');
        }
        else{
            $settings = AppMeta::select('meta_key','meta_value')->get();

            $metas = [];
            foreach ($settings as $setting){
                $metas[$setting->meta_key] = $setting->meta_value;
            }
            if(isset($metas['institute_settings'])){
                $metas['institute_settings'] = json_decode($metas['institute_settings'], true);
            }
            $appSettings = $metas;
            Cache::forever('app_settings', $metas);

        }

        if($key){
            return $appSettings[$key] ?? 0;
        }

        return $appSettings;
    }

    /**
     *
     *    site meta data settings fetch
     *
     */
    public static function getSiteMetas()
    {
        $siteMetas = null;
        if (Cache::has('site_metas')) {
            $siteMetas = Cache::get('site_metas');
        } else {

            $settings = SiteMeta::whereIn(
                'meta_key',
                [
                    'contact_address',
                    'contact_phone',
                    'contact_email',
                    'ga_tracking_id',
                ]
            )->get();

            $metas = [];
            foreach ($settings as $setting) {
                $metas[$setting->meta_key] = $setting->meta_value;
            }
            $siteMetas = $metas;
            Cache::forever('site_metas', $metas);
        }

        return $siteMetas;
    }

    /**
     *
     *    Website settings fetch
     *
     */
    public static function getWebsiteSettings()
    {
        $webSettings = null;
        if (Cache::has('website_settings')) {
            $webSettings = Cache::get('website_settings');
        } else {
            $webSettings = SiteMeta::where('meta_key', 'settings')->first();
            Cache::forever('website_settings', $webSettings);
        }

        return $webSettings;
    }

    /**
     *
     *   up comming event fetch
     *
     */
    public static function getUpcommingEvent()
    {
        $event = null;
        if (Cache::has('upcomming_event')) {
            $event = Cache::get('upcomming_event');
        } else {
            $event = Event::whereDate('event_time', '>=', date('Y-m-d'))->orderBy('event_time', 'asc')->take(1)->first();
            Cache::forever('upcomming_event', $event);
        }

        return $event;
    }

    /**
     *
     *   check is frontend website enabled
     *
     */
    public static function isFrontendEnabled()
    {
        // get app settings
        $appSettings = AppHelper::getAppSettings();
        if (isset($appSettings['frontend_website']) && $appSettings['frontend_website'] == '1') {
            return true;
        }

        return false;
    }

    /**
     * Create triggers
     * This function only used on shared hosting deployment
     */
    public static function createTriggers()
    {

        // class history table trigger
        DB::unprepared("DROP TRIGGER IF EXISTS i_class__ai;");
        DB::unprepared("DROP TRIGGER IF EXISTS i_class__au;");
        //create after insert trigger
        DB::unprepared("CREATE TRIGGER i_class__ai AFTER INSERT ON i_classes FOR EACH ROW
    INSERT INTO i_class_history SELECT 'insert', NULL, d.* 
    FROM i_classes AS d WHERE d.id = NEW.id;");
        DB::unprepared("CREATE TRIGGER i_class__au AFTER UPDATE ON i_classes FOR EACH ROW
    INSERT INTO i_class_history SELECT 'update', NULL, d.*
    FROM i_classes AS d WHERE d.id = NEW.id;");

        // section history table trigger
        DB::unprepared("DROP TRIGGER IF EXISTS section__ai;");
        DB::unprepared("DROP TRIGGER IF EXISTS section__au;");
        //create after insert trigger
        DB::unprepared("CREATE TRIGGER section__ai AFTER INSERT ON sections FOR EACH ROW
    INSERT INTO section_history SELECT 'insert', NULL, d.* 
    FROM sections AS d WHERE d.id = NEW.id;");
        DB::unprepared("CREATE TRIGGER section__au AFTER UPDATE ON sections FOR EACH ROW
    INSERT INTO section_history SELECT 'update', NULL, d.*
    FROM sections AS d WHERE d.id = NEW.id;");

        //subject history table trigger
        DB::unprepared("DROP TRIGGER IF EXISTS subject_ai;");
        DB::unprepared("DROP TRIGGER IF EXISTS subject_au;");
        //create after insert trigger
        DB::unprepared("CREATE TRIGGER subject_ai AFTER INSERT ON subjects FOR EACH ROW
    INSERT INTO subject_history SELECT 'insert', NULL, d.* 
    FROM subjects AS d WHERE d.id = NEW.id;");
        DB::unprepared("CREATE TRIGGER subject_au AFTER UPDATE ON subjects FOR EACH ROW
    INSERT INTO subject_history SELECT 'update', NULL, d.*
    FROM subjects AS d WHERE d.id = NEW.id;");

        //now create triggers for manage book stock
        DB::unprepared("DROP TRIGGER IF EXISTS book__ai;");
        DB::unprepared("DROP TRIGGER IF EXISTS book__au;");
        //book add trigger
        DB::unprepared('
			CREATE TRIGGER book__ai AFTER INSERT ON books FOR EACH ROW
			BEGIN
			insert into book_stocks
			set
			book_id = new.id,
			quantity = new.quantity;
			END
			');

        //book update trigger
        DB::unprepared('
			CREATE TRIGGER book__au AFTER UPDATE ON books FOR EACH ROW
			BEGIN
			UPDATE book_stocks
			set
			quantity = new.quantity-(old.quantity-quantity)
			WHERE book_id=old.id;
			END
			');

        DB::unprepared("DROP TRIGGER IF EXISTS book_issue__ai;");
        DB::unprepared("DROP TRIGGER IF EXISTS book_issue__au;");
        //after issue book add
        DB::unprepared('
			CREATE TRIGGER book_issue__ai AFTER INSERT ON book_issues FOR EACH ROW
			BEGIN
			UPDATE book_stocks
			set quantity = quantity-new.quantity
			where book_id=new.book_id;
			END
			');

        //after issue book update
        DB::unprepared("
			CREATE TRIGGER book_issue__au AFTER UPDATE ON book_issues FOR EACH ROW
			BEGIN
                IF (new.status = '1' AND new.status <> old.status AND new.deleted_at IS NULL AND new.deleted_by IS NULL)
                THEN
                        UPDATE book_stocks
                        set quantity = quantity+new.quantity
                        WHERE book_id=new.book_id;                      
                END IF;
                IF (new.status = '0' AND new.deleted_at IS NOT NULL AND new.deleted_by IS NOT NULL)
                THEN
                        UPDATE book_stocks
                        set quantity = quantity+new.quantity
                        WHERE book_id=new.book_id;
                END IF;
			END
			");
    }


    /**
     *
     *    Application Permission
     *
     */
    public static function getPermissions()
    {

        if (Cache::has('app_permissions')) {
            $permissions = Cache::get('app_permissions');
        } else {
            try {

                $permissions = Permission::get();
                Cache::forever('app_permissions', $permissions);
            } catch (\Illuminate\Database\QueryException $e) {
                $permissions = collect();
            }
        }

        return $permissions;
    }

    /**
     *
     *    Application users By group
     *
     */
    public static function getUsersByGroup($groupId)
    {

        try {

            $users = User::rightJoin('user_roles', 'users.id', '=', 'user_roles.user_id')
                ->where('user_roles.role_id', $groupId)
                ->select('users.id')
                ->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $users = collect();
        }


        return $users;
    }

    /**
     *
     *    Send notification to users
     *
     */
    public static function sendNotificationToUsers($users, $type, $message)
    {
        Notification::send($users, new UserActivity($type, $message));

        return true;
    }

    /**
     *
     *    Send notification to Admin users
     *
     */
    public static function sendNotificationToAdmins($type, $message)
    {
        $admins = AppHelper::getUsersByGroup(AppHelper::USER_ADMIN);
        return AppHelper::sendNotificationToUsers($admins, $type, $message);
    }

    /**
     * @param Carbon $start_date
     * @param Carbon $end_date
     * @param bool $checkWeekends
     * @param array $weekendDays
     * @return array
     */
    public static function generateDateRangeForReport(Carbon $start_date, Carbon $end_date, $checkWeekends = false, $weekendDays = [], $exludeWeekends = false)
    {


        $dates = [];
        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            if ($checkWeekends) {
                $weekend = 0;
                if (in_array($date->dayOfWeek, $weekendDays)) {
                    $weekend = 1;
                }

                if ($exludeWeekends) {
                    if (!$weekend) {
                        $dates[$date->format('Y-m-d')] = intval($date->format('d'));
                    }
                    continue;
                }

                $dates[$date->format('Y-m-d')] = [
                    'day' => intval($date->format('d')),
                    'weekend' => $weekend
                ];
            } else {
                $dates[$date->format('Y-m-d')] = intval($date->format('d'));
            }
        }

        return $dates;
    }

    /**
     * Calculate a subject's final weighted percentage from CA and Exam marks.
     *
     * @param float $caMarks
     * @param float $examMarks
     * @param \App\ExamRule $examRule
     * @param int $caWeight  (0-100, exam weight = 100 - caWeight)
     * @return float  percentage 0-100 (ceiled)
     */
    public static function calculateSubjectPercent($caMarks, $examMarks, $examRule, $caWeight)
    {
        $caTotal = $examRule->ca_total_marks ?: 1;
        $examTotal = $examRule->exam_total_marks ?: 1;

        $caPercent = (floatval($caMarks) / $caTotal) * 100;
        $examPercent = (floatval($examMarks) / $examTotal) * 100;

        $totalPercent = ceil(
            ($caPercent * $caWeight / 100) + ($examPercent * (100 - $caWeight) / 100)
        );

        if ($totalPercent < 0) {
            $totalPercent = 0;
        }
        if ($totalPercent > 100) {
            $totalPercent = 100;
        }

        return $totalPercent;
    }

    /**
     * Find grade letter from a percentage using grading rules.
     *
     * @param float $percent
     * @param array $gradingRules  decoded Grade.rules JSON (array of {grade, marks_from, marks_upto})
     * @return string grade letter, default 'F'
     */
    public static function findGradeFromPercent($percent, $gradingRules)
    {
        $grade = 'F';

        foreach ($gradingRules as $rule) {
            if ($percent >= $rule->marks_from && $percent <= $rule->marks_upto) {
                $grade = AppHelper::GRADE_TYPES[$rule->grade];
                break;
            }
        }

        return $grade;
    }

    public static function getHouseList() {
        $houseList = env('HOUSE_LIST', "");
        if(strlen($houseList)){
            $houseList = explode(',', $houseList);
            array_unshift($houseList, ' ');
            $houseList = array_combine($houseList, $houseList);
        }
        else{
            $houseList = [];
        }

        return $houseList;
    }

    public static function checkLeaveBalance($leaveType, $requestLeaveDay, $employeeId)
    {
        $holidayBalance = true;
        $message = '';
        $leaveKey = '';

        if ($leaveType == 1) {
            $leaveKey = 'total_casual_leave';
        } else if ($leaveType == 2) {
            $leaveKey = 'total_sick_leave';
        } else if ($leaveType == 4) {
            $leaveKey = 'total_maternity_leave';
        } else if ($leaveType == 5) {
            $leaveKey = 'total_special_leave';
        }

        if (strlen($leaveKey)) {
            $totalAllowLeave = AppHelper::getAppSettings($leaveKey);
            $usedLeave = Leave::where('employee_id', $employeeId)
                ->where('leave_type', $leaveType)
                ->where('status', '2')
                ->whereYear('leave_date', date('Y'))
                ->count();

            if (($requestLeaveDay + $usedLeave) > $totalAllowLeave) {
                $holidayBalance = false;
                $message = AppHelper::LEAVE_TYPES[$leaveType] . " leave limit is over. He/She took $usedLeave/$totalAllowLeave day's leave already.";
            }
        }

        return [$holidayBalance, $message];
    }

    public static function check_dev_route_access($code) {
        if ($code !== '007') {
            dd("Wrong code!");
        }

        //check if developer mode enabled?
        if (!env('DEVELOPER_MODE_ENABLED', false)) {
            dd("Please enable developer mode in '.env' file." . PHP_EOL . "set 'DEVELOPER_MODE_ENABLED=true'");
        }
    }

}
