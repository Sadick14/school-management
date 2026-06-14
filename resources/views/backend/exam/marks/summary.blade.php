<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Marks Summary @endsection
<!-- End block -->


<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Marks
            <small>Summary</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('marks.index')}}"><i class="fa icon-markmain"></i> Marks</a></li>
            <li class="active"> Summary</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset>
                                    <legend>Filters:</legend>
                                    <form novalidate id="entryForm" action="{{URL::Route('marks.summary')}}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            @if(AppHelper::getInstituteCategory() == 'college')
                                                <div class="col-md-3">
                                                    <div class="form-group has-feedback">
                                                        {!! Form::select('academic_year_id', $academic_years, $acYear , ['placeholder' => 'Pick a year...', 'class' => 'form-control select2', 'required' => 'true']) !!}
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-4">
                                                <div class="form-group has-feedback">
                                                    {!! Form::select('class_id', $classes, $class_id , ['placeholder' => 'Pick a class...', 'id' => 'class_change', 'class' => 'form-control select2', 'required' => 'true']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group has-feedback">
                                                    {!! Form::select('section_id', $sections, $section_id , ['placeholder' => 'Pick a section...','class' => 'form-control select2']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group has-feedback">
                                                    {!! Form::select('exam_id', $exams, $exam_id , ['placeholder' => 'Pick a exam...','class' => 'form-control select2', 'required' => 'true']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-info"><i class="fa fa-filter"></i> Get Summary</button>
                                            </div>
                                        </div>
                                    </form>
                                </fieldset>
                            </div>
                        </div>
                        <hr>
                        @if($students->count())
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="listDataTableWithSearch" class="table table-bordered table-striped list_view_table display responsive no-wrap" width="100%">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Student Name</th>
                                                <th>Regi No.</th>
                                                <th>Roll No.</th>
                                                @if(!$section_id)
                                                    <th>Section</th>
                                                @endif
                                                @foreach($subjects as $subject)
                                                    <th>{{ $subject->name }}</th>
                                                @endforeach
                                                <th>Entered</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($students as $student)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$student->info->name}}</td>
                                                    <td>{{$student->regi_no}}</td>
                                                    <td>{{$student->roll_no}}</td>
                                                    @if(!$section_id)
                                                        <td>{{ optional($student->section)->name }}</td>
                                                    @endif
                                                    @php $enteredCount = 0; @endphp
                                                    @foreach($subjects as $subject)
                                                        @php $mark = $student->marks->firstWhere('subject_id', $subject->id); @endphp
                                                        <td>
                                                            @if($mark)
                                                                @php $enteredCount++; @endphp
                                                                @if($mark->present == 0)
                                                                    <span class="badge bg-red">Absent</span>
                                                                @else
                                                                    CA: {{$mark->ca_marks}} / Exam: {{$mark->exam_marks}}<br>
                                                                    <strong>{{$mark->total_marks}}% ({{$mark->grade}})</strong>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">&mdash;</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td>
                                                        @if($enteredCount == $subjects->count())
                                                            <span class="badge bg-green">{{$enteredCount}}/{{$subjects->count()}}</span>
                                                        @else
                                                            <span class="badge bg-yellow">{{$enteredCount}}/{{$subjects->count()}}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Student Name</th>
                                                <th>Regi No.</th>
                                                <th>Roll No.</th>
                                                @if(!$section_id)
                                                    <th>Section</th>
                                                @endif
                                                @foreach($subjects as $subject)
                                                    <th>{{ $subject->name }}</th>
                                                @endforeach
                                                <th>Entered</th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- /.box-body -->
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
        window.section_list_url = '{{URL::Route("academic.section")}}';
        window.exam_list_url = '{{URL::Route("exam.index")}}';
        window.changeExportColumnIndex = -1;
        $(document).ready(function () {
            Academic.marksSummaryInit();
        });
    </script>
@endsection
<!-- END PAGE JS-->
