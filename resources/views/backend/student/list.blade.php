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
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#bulkEnrollModal">
                                    <i class="fa fa-users"></i> Bulk Enroll
                                </button>
                                <a class="btn btn-add-new btn-sm" href="{{ URL::route('student.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
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
                                <th width="27%">Student</th>
                                <th width="8%">Regi. No.</th>
                                <th width="10%">Class</th>
                                <th width="18%">Father's Name</th>
                                <th width="15%">Phone No</th>
                                <th width="8%">Status</th>
                                <th class="notexport" width="9%">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($students as $info)
                                <tr>
                                    <td>
                                        {{$loop->iteration}}
                                    </td>
                                    <td>
                                        <div class="avatar-name-cell">
                                            <img src="@if($info->student->photo ){{ asset('storage/student')}}/{{ $info->student->photo }} @else {{ asset('images/avatar.jpg')}} @endif" alt="">
                                            <div class="avatar-name-info">
                                                <span class="avatar-name-title">{{ $info->student->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $info->regi_no }}</td>
                                    <td>{{ $info->class->name ?? '' }}</td>
                                    <td>{{ $info->student->father_name }}</td>
                                    <td>{{ $info->student->phone_no }}</td>
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
                                <th width="27%">Student</th>
                                <th width="8%">Regi. No.</th>
                                <th width="10%">Class</th>
                                <th width="18%">Father's Name</th>
                                <th width="15%">Phone No</th>
                                <th width="8%">Status</th>
                                <th class="notexport" width="9%">Action</th>
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
    <script>
        let studentNames = [];

        $(document).ready(function() {
            // Add name on button click
            $('#addNameBtn').on('click', function(e) {
                e.preventDefault();
                addStudentName();
            });

            // Add name on Enter key
            $('#studentNameInput').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addStudentName();
                }
            });

            // Enroll button
            $('#bulkEnrollBtn').on('click', function(e) {
                e.preventDefault();
                bulkEnroll();
            });

            // Reset on modal show
            $('#bulkEnrollModal').on('show.bs.modal', function() {
                studentNames = [];
                updateStudentList();
                $('#studentNameInput').val('').focus();
            });
        });

        function addStudentName() {
            const name = $('#studentNameInput').val().trim();
            if (!name) {
                alert('Please enter a student name');
                return;
            }
            if (studentNames.includes(name)) {
                alert('This student name already added');
                return;
            }
            studentNames.push(name);
            updateStudentList();
            $('#studentNameInput').val('').focus();
        }

        function removeStudentName(index) {
            studentNames.splice(index, 1);
            updateStudentList();
        }

        function updateStudentList() {
            const $list = $('#studentNamesList');
            const count = studentNames.length;
            $('#studentCount').text(count);
            $('#bulkEnrollBtn').prop('disabled', count === 0);
            if (count === 0) {
                $list.html('<p class="text-muted">No students added yet</p>');
                return;
            }
            let html = '';
            studentNames.forEach(function(name, index) {
                html += '<span class="student-name-badge">' + name + ' <span class="remove" onclick="removeStudentName(' + index + ')">×</span></span>';
            });
            $list.html(html);
        }

        function bulkEnroll() {
            const classId = $('#bulkEnrollClass').val();
            if (!classId || !studentNames.length) {
                alert('Please select a class and add at least one student name');
                return;
            }
            $('#bulkEnrollBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enrolling...');
            $.ajax({
                url: '{{ route("student.bulk_enroll_save") }}',
                method: 'POST',
                data: {
                    class_id: classId,
                    student_names: studentNames,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        swal('Success', response.message, 'success');
                        $('#bulkEnrollModal').modal('hide');
                        setTimeout(function() {
                            window.location.href = '{{ route("student.index") }}?class=' + classId;
                        }, 1000);
                    } else {
                        swal('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An error occurred';
                    swal('Error', msg, 'error');
                },
                complete: function() {
                    $('#bulkEnrollBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Enroll All');
                }
            });
        }
    </script>
@endsection
<!-- END PAGE JS-->

@include('backend.student.bulk-enroll-modal')
