<?php

namespace App\Http\Controllers\Backend;

use App\Exam;
use App\ExamRule;
use App\Grade;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\IClass;
use App\Subject;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // check for ajax request here
        if($request->ajax()){

            //exam list by class
            $class_id = $request->query->get('class_id', 0);
            $onlyOpenForExam = $request->query->get('open_for_exam', '');

            if($class_id){
                $exams = Exam::where('status', AppHelper::ACTIVE)
                    ->where('class_id', $class_id)
                    ->if(strlen($onlyOpenForExam), 'open_for_marks_entry', '=', true)
                    ->select('name as text', 'id')
                    ->orderBy('name', 'asc')
                    ->get();

                return response()->json($exams);
            }

            // single exam details
            $exam_id = $request->query->get('exam_id', 0);
            $examInfo = Exam::select('ca_weight')
                ->where('id',$exam_id)
                ->where('status', AppHelper::ACTIVE)
                ->first();
            if($examInfo){
                return response()->json([
                    'ca_weight' => $examInfo->ca_weight,
                    'exam_weight' => 100 - $examInfo->ca_weight,
                ]);
            }
            return response('Exam not found!', 404);
        }

        $class_id = $request->query->get('class',0);
        $exams = Exam::iclass($class_id)->with('class')->get();

        $classes = IClass::where('status', AppHelper::ACTIVE)
            ->orderBy('order','asc')
            ->pluck('name', 'id');
        $iclass = $class_id;

        return view('backend.exam.list', compact('exams','classes', 'iclass'));
    }

    /**
     * Display a listing exam for public use
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPublic(Request $request)
    {
        // check for ajax request here
        if($request->ajax()){

            //exam list by class
            $class_id = $request->query->get('class_id', 0);
            if($class_id){
                $exams = Exam::where('status', AppHelper::ACTIVE)
                    ->where('class_id', $class_id)
                    ->select('name as text', 'id')
                    ->orderBy('name', 'asc')->get();

                return response()->json($exams);
            }

            // single exam details
            $exam_id = $request->query->get('exam_id', 0);
            $examInfo = Exam::select('ca_weight')
                ->where('id',$exam_id)
                ->where('status', AppHelper::ACTIVE)
                ->first();
            if($examInfo){
                return response()->json([
                    'ca_weight' => $examInfo->ca_weight,
                    'exam_weight' => 100 - $examInfo->ca_weight,
                ]);
            }
            return response('Exam not found!', 404);
        }

        return response('Bad request!', 400);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $exam = null;

        $classes = IClass::where('status', AppHelper::ACTIVE)
            ->orderBy('order','asc')
            ->pluck('name', 'id');

        $open_for_marks_entry = 0;

        return view('backend.exam.add', compact('exam','classes','open_for_marks_entry'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate(
            $request, [
                'name' => 'required|max:255',
                'class_id' => 'required|integer',
                'ca_weight' => 'required|integer|min:0|max:100',
            ]
        );


        $data = $request->all();
        if($request->has('open_for_marks_entry')){
            $data['open_for_marks_entry'] = true;
        }

        // now save employee
        Exam::create($data);

        //now notify the admins about this record
        $msg = $data['name']." exam added by ".auth()->user()->name;
        $nothing = AppHelper::sendNotificationToAdmins('info', $msg);
        // Notification end

        return redirect()->route('exam.create')->with('success', 'Exam added!');

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id integer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        //todo: need protection to massy events. like modify after used or delete after user

        $open_for_marks_entry = $exam->open_for_marks_entry;

        return view('backend.exam.add', compact('exam', 'open_for_marks_entry'));


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $exam = Exam::findOrFail($id);
        //todo: need protection to massy events. like modify after used or delete after user
        $this->validate(
            $request, [
                'name' => 'required|max:255',
                'ca_weight' => 'required|integer|min:0|max:100',
            ]
        );


        $data = $request->all();
        unset($data['class_id']);
        if($request->has('open_for_marks_entry')){
            $data['open_for_marks_entry'] = true;
        }
        else {
            $data['open_for_marks_entry'] = false;
        }


        $exam->fill($data);
        $exam->save();

        return redirect()->route('exam.index')->with('success', 'Exam Updated!');
    }


    /**
     * Destroy the resource
     */
    public function destroy($id) {
        $exam = Exam::findOrFail($id);
        $exam->delete();
        //todo: need protection to massy events. like modify after used or delete after user
        return redirect()->route('exam.index')->with('success', 'Exam Deleted!');
    }

    /**
     * status change
     * @return mixed
     */
    public function changeStatus(Request $request, $id=0)
    {

        $exam =  Exam::findOrFail($id);
        if(!$exam){
            return [
                'success' => false,
                'message' => 'Record not found!'
            ];
        }

        if($request->has('open_entry')){
            $exam->open_for_marks_entry = $request->get('status');
            $exam->save();

            return [
                'success' => true,
                'message' => 'Record updated.'
            ];
        }

        $exam->status = (string)$request->get('status');
        $exam->save();

        return [
            'success' => true,
            'message' => 'Status updated.'
        ];

    }


    /**
     * grade  manage
     * @return \Illuminate\Http\Response
     */
    public function gradeIndex(Request $request)
    {
        //for save on POST request
        if ($request->isMethod('post')) {//
            $this->validate($request, [
                'hiddenId' => 'required|integer',
            ]);
            $grade = Grade::findOrFail($request->get('hiddenId'));
            $haveRules = ExamRule::where('grade_id', $grade->id)->count();
            if($haveRules){
                return redirect()->route('exam.grade.index')->with('error', 'Can not delete! Grade used in exam rules.');
            }

            $grade->delete();

            //now notify the admins about this record
            $msg = $grade->name." grade deleted by ".auth()->user()->name;
            $nothing = AppHelper::sendNotificationToAdmins('info', $msg);
            // Notification end

            return redirect()->route('exam.grade.index')->with('success', 'Record deleted!');
        }

        //for get request
        $grades = Grade::get();
        return view('backend.exam.grade.list', compact('grades'));
    }

    /**
     * grade create, read, update manage
     * @return \Illuminate\Http\Response
     */
    public function gradeCru(Request $request, $id=0)
    {
        //for save on POST request
        if ($request->isMethod('post')) {

            //protection to prevent massy event. Like edit grade after its use in rules
            // or marks entry
            if($id){
                $grade = Grade::find($id);
                //if grade use then can't edit it
                if($grade) {
                    $haveRules = ExamRule::where('grade_id', $grade->id)->count();
                    if ($haveRules) {
                        return redirect()->route('exam.grade.index')->with('error', 'Can not Edit! Grade used in exam rules.');
                    }
                }
            }

            $this->validate($request, [
                'name' => 'required|max:255',
                'grade' => 'required|array',
                'marks_from' => 'required|array',
                'marks_upto' => 'required|array',
            ]);

            $rules = [];
            $inputs = $request->all();
            foreach ($inputs['grade'] as $key => $value){
                $rules[] = [
                    'grade' => $value,
                    'marks_from' => $inputs['marks_from'][$key],
                    'marks_upto' => $inputs['marks_upto'][$key]
                ];
            }

            $data = [
                'name' => $request->get('name'),
                'rules' => json_encode($rules)
            ];

            Grade::updateOrCreate(
                ['id' => $id],
                $data
            );

            if(!$id){
                //now notify the admins about this record
                $msg = $data['name']." graded added by ".auth()->user()->name;
                $nothing = AppHelper::sendNotificationToAdmins('info', $msg);
                // Notification end
            }


            $msg = "Grade ";
            $msg .= $id ? 'updated.' : 'added.';

            return redirect()->route('exam.grade.index')->with('success', $msg);
        }

        //for get request
        $grade = Grade::find($id);

        //if grade use then can't edit it
        if($grade) {
            $haveRules = ExamRule::where('grade_id', $grade->id)->count();
            if ($haveRules) {
                return redirect()->route('exam.grade.index')->with('error', 'Can not Edit! Grade used in exam rules.');
            }
        }

        return view('backend.exam.grade.add', compact('grade'));
    }

    /**
     * rule  manage
     * @return \Illuminate\Http\Response
     */
    public function ruleIndex(Request $request)
    {
        //for save on POST request
        if ($request->isMethod('post')) {//
            $this->validate($request, [
                'hiddenId' => 'required|integer',
            ]);
            $rules = ExamRule::findOrFail($request->get('hiddenId'));
            $rules->delete();

            //now notify the admins about this record
            $msg = "Exam rules deleted by ".auth()->user()->name;
            $nothing = AppHelper::sendNotificationToAdmins('info', $msg);
            // Notification end

            return redirect()->route('exam.rule.index')->with('success', 'Record deleted!');
        }

        //for get request
        $rules = collect();

        $class_id = $request->query->get('class_id',0);
        $exam_id = $request->query->get('exam_id',0);

        if($class_id && $exam_id){
            $rules = ExamRule::where('class_id', $class_id)
                ->where('exam_id', $exam_id)
                ->with(['subject' => function($query){
                    $query->select('name','id');
                }])
                ->with(['grade' => function($query){
                    $query->select('name','id');
                }])
                ->get();
        }

        $classes = IClass::where('status', AppHelper::ACTIVE)
            ->orderBy('order','asc')
            ->pluck('name', 'id');
        $exams = Exam::where('class_id', $class_id)
            ->where('status', AppHelper::ACTIVE)
            ->pluck('name', 'id');;

        return view('backend.exam.rule.list', compact('rules', 'classes', 'exams','class_id','exam_id'));
    }

    /**
     * rule create, read manage
     * @return \Illuminate\Http\Response
     */
    public function ruleCreate(Request $request)
    {
        //for save on POST request
        if ($request->isMethod('post')) {
            $validateRules = [
                'class_id' => 'required|integer',
                'subject_id' => 'required|integer',
                'exam_id' => 'required|integer',
                'grade_id' => 'required|integer',
                'ca_total_marks' => 'required|integer|min:1',
                'exam_total_marks' => 'required|integer|min:1',
                'pass_mark' => 'required|integer|min:0|max:100',
            ];

            $this->validate($request, $validateRules);

            $inputs = $request->all();

            //validation check of existing rule
            $entryExists = ExamRule::where('subject_id', $inputs['subject_id'])
                ->where('exam_id', $inputs['exam_id'])->count();
            if($entryExists){
                return redirect()->route('exam.rule.create')->with('error', 'Rule already exists for this subject and exam!');
            }

            //validation end

            ExamRule::create($inputs);


            //now notify the admins about this record
            $msg = "Exam rule added by ".auth()->user()->name;
            $nothing = AppHelper::sendNotificationToAdmins('info', $msg);
            // Notification end

            $msg = "New exam rule added.";
            return redirect()->route('exam.rule.create')->with('success', $msg);
        }

        //for get request
        $rule = null;
        $subject_id = null;
        $exam_id = null;
        $grade_id = null;

        $classes = IClass::where('status', AppHelper::ACTIVE)
            ->orderBy('order','asc')
            ->pluck('name', 'id');
        $exams = [];//Exam::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');
        $subjects = [];


        return view('backend.exam.rule.add', compact('rule',
            'subject_id',
            'exam_id',
            'grade_id',
            'classes',
            'exams',
            'grades',
            'subjects'
        ));
    }

    /**
     * rule update and edit manage
     * @return \Illuminate\Http\Response
     */
    public function ruleEdit(Request $request, $id=0){

        $rule = ExamRule::findOrFail($id);


        //for save on POST request
        if ($request->isMethod('post')) {
            $validateRules = [
                'exam_id' => 'required|integer',
                'grade_id' => 'required|integer',
                'ca_total_marks' => 'required|integer|min:1',
                'exam_total_marks' => 'required|integer|min:1',
                'pass_mark' => 'required|integer|min:0|max:100',
            ];

            $this->validate($request, $validateRules);

            $inputs = $request->all();
            unset($inputs['subject_id']);
            //validation end

            $rule->fill($inputs);
            $rule->save();


            //now notify the admins about this record
            $msg = "Exam rule updated by ".auth()->user()->name;
            $nothing = AppHelper::sendNotificationToAdmins('info', $msg);
            // Notification end

            $msg = "Exam rule updated.";
            return redirect()->route('exam.rule.index')->with('success', $msg);
        }


        $subject_id = $rule->subject_id;
        $exam_id = $rule->exam_id;
        $grade_id = $rule->grade_id;

        $subjects = Subject::where('class_id', $rule->class_id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('order','asc')
            ->pluck('name', 'id');
        $exams = Exam::where('class_id', $rule->class_id)
            ->where('status', AppHelper::ACTIVE)
            ->pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');;

        return view('backend.exam.rule.add', compact('rule',
            'subject_id',
            'exam_id',
            'grade_id',
            'subjects',
            'exams',
            'grades'
        ));

    }


}
