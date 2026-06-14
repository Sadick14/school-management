<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Grade @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Grade
            <small>@if($grade) Update @else Add New @endif</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('exam.grade.index')}}"><i class="fa fa-bar-chart"></i> Grade</a></li>
            <li class="active">@if($grade) Update @else Add @endif</li>
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
                            <p><b>Note:</b> If not need any of the rule then leave as it is.</p>
                        </div>
                    </div>
                    <form novalidate id="entryForm" action="@if($grade) {{URL::Route('exam.grade.update', $grade->id)}} @else {{URL::Route('exam.grade.store')}} @endif" method="post" enctype="multipart/form-data">
                        @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group has-feedback">
                                    <label for="name">Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" placeholder="name" value="@if($grade){{ $grade->name }}@else{{ old('name') }}@endif" required maxlength="255">
                                    <span class="fa fa-info form-control-feedback"></span>
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group has-feedback">
                                    <label>Grading Rules<span class="text-danger">*</span>
                                    </label>
                                    <table class="table table-striped table-bordered haveForm">
                                        <thead>
                                        <tr>
                                            <th>
                                                Grade
                                            </th>
                                            <th>
                                                Remark
                                            </th>
                                            <th>
                                                Marks From
                                            </th>
                                            <th>
                                                Marks Upto
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $defaultRanges = [
                                                    1 => ['marks_from' => 80, 'marks_upto' => 100],
                                                    2 => ['marks_from' => 70, 'marks_upto' => 79],
                                                    3 => ['marks_from' => 60, 'marks_upto' => 69],
                                                    4 => ['marks_from' => 50, 'marks_upto' => 59],
                                                    5 => ['marks_from' => 40, 'marks_upto' => 49],
                                                    6 => ['marks_from' => 0, 'marks_upto' => 39],
                                                ];

                                                $formatedRules = [];
                                                if($grade){
                                                    $rules = json_decode($grade->rules);
                                                    foreach ($rules as $rule){
                                                        $formatedRules[$rule->grade] = ['marks_from' => $rule->marks_from,'marks_upto' => $rule->marks_upto];
                                                    }
                                                }
                                            @endphp
                                            @foreach(AppHelper::GRADE_TYPES as $key => $rgrade)
                                                <tr>
                                                    <td>
                                                        <span>{{$rgrade}}</span>
                                                        <input type="hidden" name="grade[]" value="{{$key}}">
                                                    </td>
                                                    <td>
                                                        <span>{{ AppHelper::GRADE_REMARKS[$key] }}</span>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="marks_from[]" value="@if($grade){{$formatedRules[$key]['marks_from']}}@else{{$defaultRanges[$key]['marks_from']}}@endif" placeholder="" required min="0" max="100">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="marks_upto[]" value="@if($grade){{$formatedRules[$key]['marks_upto']}}@else{{$defaultRanges[$key]['marks_upto']}}@endif" placeholder="" required min="0" max="100">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <span class="text-danger">{{ $errors->first('grade') }}</span>
                                    <span class="text-danger">{{ $errors->first('marks_from') }}</span>
                                    <span class="text-danger">{{ $errors->first('marks_upto') }}</span>
                                </div>
                            </div>


                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <a href="{{URL::route('exam.index')}}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-info pull-right"><i class="fa @if($grade) fa-refresh @else fa-plus-circle @endif"></i> @if($grade) Update @else Add @endif</button>
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
        });
    </script>
@endsection
<!-- END PAGE JS-->
