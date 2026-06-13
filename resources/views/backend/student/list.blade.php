<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Student @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Student
            <small>List</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Student</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        <form id="studentFilterForm" method="GET" action="{{ URL::route('student.index') }}">
                            @if(AppHelper::getInstituteCategory() == 'college')
                            <div class="col-md-3">
                            <div class="form-group has-feedback">
                                    {!! Form::select('academic_year', $academic_years, $acYear , ['placeholder' => 'Pick a year...','class' => 'form-control select2 auto-submit', 'required' => 'true']) !!}
                                </div>
                            </div>
                            @endif
                            <div class="col-md-3">
                            <div class="form-group has-feedback">
                                    {!! Form::select('class', $classes, $iclass , ['placeholder' => 'Pick a class...','class' => 'form-control select2 auto-submit', 'id' => 'class_select', 'required' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group has-feedback">
                                    {!! Form::select('section', $sections, $section_id , ['placeholder' => 'Pick a section...','class' => 'form-control select2 auto-submit', 'id' => 'student_list_filter', 'required' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group has-feedback">
                                    {!! Form::select('status', ['1' => "Active", '0' => 'Deactivate'], $status , ['class' => 'form-control select2 auto-submit', 'required' => 'true']) !!}
                                </div>
                            </div>
                            <div class="box-tools pull-right">
                                <a class="btn btn-info btn-sm" href="{{ URL::route('student.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                        <table id="listDataTableWithSearch" class="table table-bordered table-striped list_view_table display responsive no-wrap" width="100%">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th class="notexport" width="7%">Photo</th>
                                <th width="8%">Regi. No.</th>
                                <th width="8%">Roll No.</th>
                                <th width="8%">ID Card</th>
                                <th width="19%">Name</th>
                                <th width="10%">Phone No</th>
                                <th width="10%">Email</th>
                                <th width="10%">Status</th>
                                <th class="notexport" width="15%">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($students as $info)
                                <tr>
                                    <td>
                                        {{$loop->iteration}}
                                    </td>
                                    <td>
                                        <img class="img-responsive center" style="height: 35px; width: 35px;" src="@if($info->student->photo ){{ asset('storage/student')}}/{{ $info->student->photo }} @else {{ asset('images/avatar.jpg')}} @endif" alt="">
                                    </td>
                                    <td>{{ $info->regi_no }}</td>
                                    <td>{{ $info->roll_no }}</td>
                                    <td>{{ $info->card_no }}</td>
                                    <td>{{ $info->student->name }}</td>
                                    <td>{{ $info->student->phone_no }}</td>
                                    <td>{{ $info->student->email }}</td>
                                    <td>
                                        <!-- todo: have problem in mobile device -->
                                        <input class="statusChange" type="checkbox" data-pk="{{$info->id}}" @if($info->status) checked @endif data-toggle="toggle" data-on="<i class='fa fa-check-circle'></i>" data-off="<i class='fa fa-ban'></i>" data-onstyle="success" data-offstyle="danger">
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a title="Details"  href="{{URL::route('student.show',$info->id)}}"  class="btn btn-primary btn-sm"><i class="fa fa-eye"></i>
                                            </a>
                                        </div>
                                        @if($info->is_promoted == '0')
                                            <div class="btn-group">
                                                <a title="Edit" href="{{URL::route('student.edit',$info->id)}}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                                </a>
                                            </div>
                                            <!-- todo: have problem in mobile device -->
                                            <div class="btn-group">
                                                <form  class="myAction" method="POST" action="{{URL::route('student.destroy', $info->id)}}">
                                                    @csrf
                                                    <input name="_method" type="hidden" value="DELETE">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fa fa-fw fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                            <tfoot>
                            <tr>
                                <th width="5%">#</th>
                                <th class="notexport" width="7%">Photo</th>
                                <th width="8%">Regi. No.</th>
                                <th width="8%">Roll No.</th>
                                <th width="8%">ID Card</th>
                                <th width="19%">Name</th>
                                <th width="10%">Phone No</th>
                                <th width="10%">Email</th>
                                <th width="10%">Status</th>
                                <th class="notexport" width="15%">Action</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
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
        $(document).ready(function () {
            window.postUrl = '{{URL::Route("student.status", 0)}}';
            window.section_list_url = '{{URL::Route("academic.section")}}';
           Academic.studentInit();
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var classSelect = document.querySelector('[name="class"]');
        var studentFilterSelect = document.getElementById('student_list_filter');
        var studentFilterForm = document.getElementById('studentFilterForm');

        if (classSelect) {
            classSelect.addEventListener('change', function() {
                // When class changes, reload sections via AJAX
                var classId = this.value;

                if (classId) {
                    // Fetch sections for this class
                    fetch('{{ URL::route("student.index") }}?class=' + classId, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Extract the sections dropdown from the response
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        var newSectionSelect = doc.querySelector('[name="section"]');

                        if (newSectionSelect && studentFilterSelect) {
                            // Replace the section select options
                            studentFilterSelect.innerHTML = newSectionSelect.innerHTML;

                            // Reinitialize Select2 if it exists
                            if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                                jQuery(studentFilterSelect).select2();
                            }
                        }

                        // Auto-submit the form to load students
                        if (studentFilterForm) {
                            studentFilterForm.submit();
                        }
                    })
                    .catch(error => console.log('Error loading sections:', error));
                }
            });
        }

        // Auto-submit form on other filter changes
        var autoSubmitSelects = document.querySelectorAll('.auto-submit');
        autoSubmitSelects.forEach(function(select) {
            select.addEventListener('change', function() {
                // Only auto-submit if not the class selector (already handled above)
                if (this.name !== 'class' && this.value) {
                    if (studentFilterForm) {
                        studentFilterForm.submit();
                    }
                }
            });
        });
    });
    </script>
@endsection
<!-- END PAGE JS-->
