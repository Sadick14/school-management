<?php

use Faker\Generator as Faker;

$factory->define(App\Exam::class, function (Faker $faker) {
    $names = ['Class Test 1', 'Class Test 2', 'Mid-Term Exam', 'End of Term Exam', 'Mock Exam', 'Project Work'];
    return [
        'name' => $names[array_rand($names)],
        'elective_subject_point_addition' => rand(0,2),
        'marks_distribution_types' => json_encode(array_rand(\App\Http\Helpers\AppHelper::MARKS_DISTRIBUTION_TYPES, 3)),
        'class_id' => function () {
            // Get random class id
            return App\IClass::where('id','!=',1)->inRandomOrder()->first()->id;
        },
        'status' => '1',
    ];
});
