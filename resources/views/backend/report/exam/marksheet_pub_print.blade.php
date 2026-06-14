@extends('backend.report.layouts.master', ['headerData' => $headerData,'printIt' => 1])
@section('extraStyle')
    <style>
        @page {
            size:  A4 portrait;
        }
    </style>
@endsection
@section('reportBody')
    <div class="report-body">
        <div class="report-filter">
            <div class="row">
                <div class="col-xs-2"></div>
                <div class="col-xs-6">
                    <h6><span class="text-bold">Result Publish Date:</span> {{$result->published_at}}</h6>
                    <h6><span class="text-bold">Student Name:</span> {{$student->info->name}}</h6>
                    <h6><span class="text-bold">Class:</span> {{$student->class->name}}</h6>
                </div>
                <div class="col-xs-2" style="">
                    <h6><span class="text-bold">Section:</span> {{ optional($student->section)->name }}</h6>
                    <h6><span class="text-bold">Regi No:</span> {{$student->regi_no}}</h6>
                    <h6><span class="text-bold">Roll No:</span> {{$student->roll_no}}</h6>
                </div>


            </div>
        </div>
        <div class="report-data">
            <h5 class="text-bold">Result Details:</h5>
            <div class="row">
                <div class="col-xs-12">
                    <table class="main-data">
                        <thead>
                        <tr>
                            <th width="3%">SL</th>
                            <th>Code</th>
                            <th>Sub Name</th>
                            <th>CA Marks</th>
                            <th>Exam Marks</th>
                            <th>Highest</th>
                            <th>Total %</th>
                            <th>Grade</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($coreSubjectsMarks as $subjectMarks)
                            <tr @if($loop->last) class="lastItem" @endif>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$subjectMarks['code']}}</td>
                                <td>{{$subjectMarks['name']}}</td>
                                <td>{{$subjectMarks['ca_marks']}}</td>
                                <td>{{$subjectMarks['exam_marks']}}</td>
                                <td><b>{{$subjectMarks['highest_marks']}}</b></td>
                                <td><b>{{$subjectMarks['total_marks']}}</b></td>
                                <td><b>{{$subjectMarks['grade']}}</b></td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>&nbsp;</th>
                            <th class="text-bold" style="text-align: right;" colspan="5">Overall Total %</th>
                            <th class="text-bold">{{$result->total_marks}}</th>
                            <th class="text-bold">{{$result->grade}}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="report-authority" style="margin-top: 80px;">
            <div class="row">
                <div class="col-xs-4">
                    <h5 style="text-align: left;">Guarding Signature</h5>
                </div>
                <div class="col-xs-4">
                </div>
                <div class="col-xs-4" style="text-align: right;">
                    <h5 style="text-align: right;">@if(AppHelper::getInstituteCategory() == 'college') {{'Principal'}} @else {{'Headmaster'}} @endif Signature</h5>
                </div>
            </div>
        </div>
    </div>
@endsection