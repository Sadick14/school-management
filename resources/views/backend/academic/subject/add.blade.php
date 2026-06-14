<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Subject @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Subject
            <small>@if($subject) Update @else Add New @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('academic.subject')}}"><i class="fa fa-cubes"></i> Subject</a></li>
            <li class="active">@if($subject) Update @else Add @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate id="entryForm" action="@if($subject) {{URL::Route('academic.subject_update', $subject->id)}} @else {{URL::Route('academic.subject_store')}} @endif" method="post" enctype="multipart/form-data">
                        <div class="box-header">
                            <div class="callout callout-danger">
                                <p><b>Note:</b> Create a teacher and class before create new subject.</p>
                            </div>
                        </div>
                        <div class="box-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="name">Name<span class="text-danger">*</span></label>
                                        <input autofocus type="text" class="form-control" name="name" placeholder="name" value="@if($subject){{ $subject->name }}@else{{ old('name') }} @endif" required minlength="1" maxlength="255">
                                        <span class="fa fa-info form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group has-feedback">
                                        <label for="code">Code<span class="text-danger">*</span>
                                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Code can be numeric (100) or alphanumeric (ENG)"></i>
                                        </label>
                                        <input type="text" class="form-control" name="code" placeholder="ENG or 100" value="@if($subject){{ $subject->code }}@else{{ old('code') }}@endif" required minlength="1" maxlength="50">
                                        <span class="fa fa-code form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('code') }}</span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="teacher_id">Teacher Name<span class="text-danger">*</span>
                                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Set subject teacher"></i>
                                        </label>
                                        {!! Form::select('teacher_id[]', $teachers, $teacher_ids , ['multiple' => 'true', 'data-placeholder' => 'select teacher' ,'class' => 'form-control select2', 'required' => 'true']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('teacher_id') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group has-feedback">
                                        <label for="open_for_marks_entry">Exclude in Result
                                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Check it, if this subject is not add in result calculation."></i>
                                            <div class="checkbox icheck">
                                                <label>
                                                    {!! Form::checkbox('exclude_in_result', $exclude_in_result, $exclude_in_result) !!}
                                                </label>
                                            </div>
                                        </label>
                                        <span class="text-danger">{{ $errors->first('exclude_in_result') }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{URL::route('academic.subject')}}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info pull-right"><i class="fa @if($subject) fa-refresh @else fa-plus-circle @endif"></i> @if($subject) Update @else Add @endif</button>

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
        $(document).ready(function () {
            Academic.subjectInit();
        });
    </script>
@endsection
<!-- END PAGE JS-->
