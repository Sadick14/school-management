<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Exam Rules @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Exam Rules
            <small>@if($rule) Update @else Add New @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('exam.rule.index')}}"><i class="fa fa-bar-chart"></i> Exam Rules</a></li>
            <li class="active">@if($rule) Update @else Add @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        <div class="callout callout-danger">
                            <p><b>Note:</b> Create class,subejct,exam and grade before add exam rule.</p>
                        </div>
                    </div>
                    <form novalidate id="entryForm" action="@if($rule) {{URL::Route('exam.rule.update', $rule->id)}} @else {{URL::Route('exam.rule.store')}} @endif" method="post" enctype="multipart/form-data">
                        @csrf
                    <div class="box-body">

                        <div class="row">
                            @if(!$rule)
                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    <label for="class_id">Class Name<span class="text-danger">*</span></label>
                                        {!! Form::select('class_id', $classes, null , ['id' => 'exam_rules_add_class_change', 'placeholder' => 'Pick a class...','class' => 'form-control select2', 'required' => 'true']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('class_id') }}</span>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-3">
                                @php
                                    $readonly = [];
                                    if($rule){
                                        $readonly = ['readonly' => true];
                                     }
                                @endphp
                                <div class="form-group has-feedback">
                                    <label for="subject_id">Subject<span class="text-danger">*</span></label>
                                    {!! Form::select('subject_id', $subjects, $subject_id , ['placeholder' => 'Pick a subject...', 'class' => 'form-control select2', 'required' => 'true'] + $readonly) !!}
                                    <span class="fa form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('subject_id') }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    <label for="exam_id">Exam<span class="text-danger">*</span></label>
                                    {!! Form::select('exam_id', $exams, $exam_id , ['placeholder' => 'Pick a exam...','class' => 'form-control select2', 'required' => 'true']) !!}
                                    <span class="fa form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('exam_id') }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    <label for="grade_id">Marks Grading<span class="text-danger">*</span></label>
                                    {!! Form::select('grade_id', $grades, $grade_id , ['placeholder' => 'Pick a grade...','class' => 'form-control select2', 'required' => 'true']) !!}
                                    <span class="fa form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('grade_id') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    <label for="ca_total_marks">CA Total Marks<span class="text-danger">*</span>
                                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="The maximum marks for Continuous Assessment (CA) for this subject."></i>
                                    </label>
                                    <input type="number" class="form-control" name="ca_total_marks" value="@if($rule){{$rule->ca_total_marks}}@else{{100}}@endif" required min="1">
                                    <span class="form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('ca_total_marks') }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    <label for="exam_total_marks">Exam Total Marks<span class="text-danger">*</span>
                                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="The maximum marks for the Exam for this subject."></i>
                                    </label>
                                    <input type="number" class="form-control" name="exam_total_marks" value="@if($rule){{$rule->exam_total_marks}}@else{{100}}@endif" required min="1">
                                    <span class="form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('exam_total_marks') }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    <label for="pass_mark">Pass Mark (%)<span class="text-danger">*</span>
                                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="The minimum overall percentage required to pass this subject."></i>
                                    </label>
                                    <input type="number" class="form-control" name="pass_mark" value="@if($rule){{$rule->pass_mark}}@else{{40}}@endif" required min="0" max="100">
                                    <span class="form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('pass_mark') }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    <label for="exam_weight_info">Exam CA:Exam Weight</label>
                                    <input id="exam_weight_info" type="text" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <a href="{{URL::route('exam.rule.index')}}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-info pull-right"><i class="fa @if($rule) fa-refresh @else fa-plus-circle @endif"></i> @if($rule) Update @else Add @endif</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        window.subject_list_url = '{{URL::Route("academic.subject")}}';
        window.exam_details_url = '{{URL::Route("exam.index")}}';
        window.exam_list_url = '{{URL::Route("exam.index")}}';
        $(document).ready(function () {
            Academic.examRuleInit();
            @if($rule)
                $('select[name="subject_id"]').prop('readonly', true);
            @endif
        });
    </script>
@endsection
<!-- END PAGE JS-->
