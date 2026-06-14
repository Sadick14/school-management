<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Exam @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Exam
            <small>@if($exam) Update @else Add New @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('exam.index')}}"><i class="fa fa-external-link"></i> Exam</a></li>
            <li class="active">@if($exam) Update @else Add @endif</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <form novalidate id="entryForm" action="@if($exam) {{URL::Route('exam.update', $exam->id)}} @else {{URL::Route('exam.store')}} @endif" method="post" enctype="multipart/form-data">
                        @csrf
                    <div class="box-body">
                        <div class="row">
                            @if(!$exam)
                                <div class="col-md-4">
                                    <div class="form-group has-feedback">
                                        <label for="class_id">Class Name<span class="text-danger">*</span></label>
                                        {!! Form::select('class_id', $classes, null , ['placeholder' => 'Pick a class...','class' => 'form-control select2', 'required' => 'true']) !!}
                                        <span class="form-control-feedback"></span>
                                        <span class="text-danger">{{ $errors->first('class_id') }}</span>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="name">Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" placeholder="name" value="@if($exam){{ $exam->name }}@else{{ old('name') }}@endif" required maxlength="255">
                                    <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="ca_weight">CA Weight (%)<span class="text-danger">*</span>
                                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="The percentage weight given to Continuous Assessment (CA) marks when calculating a subject's final percentage. The remaining percentage is the Exam weight."></i>
                                    </label>
                                    <input id="ca_weight" type="number" min="0" max="100" class="form-control" name="ca_weight" placeholder="CA Weight" value="@if($exam){{ $exam->ca_weight }}@else{{30}}@endif" required>
                                    <span class="fa fa-percent form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('ca_weight') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group has-feedback">
                                    <label for="exam_weight_display">Exam Weight (%)</label>
                                    <input id="exam_weight_display" type="text" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group has-feedback">
                                    <label for="open_for_marks_entry">Open for marks entry
                                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="If not check it then exam will not show in marks entry form. So before marks entry check it. Its helps to protect from accidental marks entry in wrong exam."></i>
                                        <div class="checkbox icheck">
                                            <label>
                                                {!! Form::checkbox('open_for_marks_entry', $open_for_marks_entry, $open_for_marks_entry) !!}
                                            </label>
                                        </div>
                                    </label>
                                    <span class="text-danger">{{ $errors->first('open_for_marks_entry') }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <a href="{{URL::route('exam.index')}}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-info pull-right"><i class="fa @if($exam) fa-refresh @else fa-plus-circle @endif"></i> @if($exam) Update @else Add @endif</button>
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
           Generic.initCommonPageJS();

           var $caWeight = $('#ca_weight');
           var $examWeightDisplay = $('#exam_weight_display');

           var updateExamWeight = function () {
               var caWeight = parseInt($caWeight.val(), 10);
               if (isNaN(caWeight)) {
                   $examWeightDisplay.val('');
                   return;
               }
               $examWeightDisplay.val((100 - caWeight) + '%');
           };

           $caWeight.on('input change keyup', updateExamWeight);
           updateExamWeight();
        });
    </script>
@endsection
<!-- END PAGE JS-->
