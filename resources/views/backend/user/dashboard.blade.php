<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Dashboard @endsection
<!-- End block -->
@section('extraStyle')
    <style>
        .notification li {
            font-size: 16px;
        }
        .notification li.info span.badge {
            background: #00c0ef;
        }
        .notification li.warning span.badge {
            background: #f39c12;
        }
        .notification li.success span.badge {
            background: #00a65a;
        }
        .notification li.error span.badge {
            background: #dd4b39;
        }
        .total_bal {
            margin-top: 5px;
            margin-right: 5%;
        }
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Main content -->
    <section class="content">
        <!-- Modern Hero Banner -->
        <div class="dashboard-hero-banner">
            <div class="dashboard-hero-content">
                <h2>Good Morning, {{ auth()->user()->name }}!</h2>
                <p>Welcome back to your dashboard. You can manage student records, monitor attendance, configure schedules, and review academic reports from one place.</p>
                <a href="{{ URL::route('student.index') }}" class="dashboard-hero-btn">View Students</a>
            </div>
            <!-- Beautiful modern inline SVG illustration -->
            <svg class="dashboard-hero-illustration hidden-xs" viewBox="0 0 300 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Graduate / Teacher visual representation -->
                <circle cx="150" cy="100" r="80" fill="white" fill-opacity="0.1"/>
                <circle cx="150" cy="100" r="50" fill="white" fill-opacity="0.15"/>
                <!-- Graduate Cap -->
                <path d="M150 50L210 70L150 90L90 70L150 50Z" fill="#ffffff" fill-opacity="0.9"/>
                <path d="M110 77V105C110 115 128 123 150 123C172 123 190 115 190 105V77" fill="#ffffff" fill-opacity="0.8"/>
                <rect x="203" y="73" width="6" height="40" rx="2" fill="#ffffff" fill-opacity="0.75"/>
                <circle cx="206" cy="115" r="5" fill="#facc15"/>
                <!-- Decorative stars/shapes -->
                <path d="M70 40L73 47L80 48L75 53L76 60L70 56L64 60L65 53L60 48L67 47L70 40Z" fill="#facc15" fill-opacity="0.8"/>
                <path d="M230 140L232 145L237 146L233 150L234 155L230 152L226 155L227 150L223 146L228 145L230 140Z" fill="#ffffff" fill-opacity="0.8"/>
            </svg>
        </div>

        @if($userRoleId == AppHelper::USER_ADMIN)
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <a href="{{URL::route('student.index')}}" class="modern-stat-card-link">
                        <div class="modern-stat-card stat-color-orange">
                            <div class="stat-icon-wrapper">
                                <i class="fa icon-student"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">{{$students}}</h3>
                                <p class="stat-label">Students</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <a href="{{URL::route('teacher.index')}}" class="modern-stat-card-link">
                        <div class="modern-stat-card stat-color-pink">
                            <div class="stat-icon-wrapper">
                                <i class="fa icon-teacher"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">{{$teachers}}</h3>
                                <p class="stat-label">Teachers</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <a href="{{URL::route('hrm.employee.index')}}" class="modern-stat-card-link">
                        <div class="modern-stat-card stat-color-purple">
                            <div class="stat-icon-wrapper">
                                <i class="fa icon-member"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">{{$employee}}</h3>
                                <p class="stat-label">Employees</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <a href="{{URL::route('academic.subject')}}" class="modern-stat-card-link">
                        <div class="modern-stat-card stat-color-teal">
                            <div class="stat-icon-wrapper">
                                <i class="fa icon-subject"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">{{$subjects}}</h3>
                                <p class="stat-label">Subjects</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif

        @if($userRoleId != AppHelper::USER_STUDENT)
        <div class="row">
            {{--<div class="col-md-6">--}}
            {{--<div class="box box-primary">--}}
            {{--<div class="box-body">--}}
            {{--<!-- THE CALENDAR -->--}}
            {{--<div id="calendar"></div>--}}
            {{--</div>--}}
            {{--<!-- /.box-body -->--}}
            {{--</div>--}}
            {{--</div>--}}
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border x_title">
                        <h3>Students Today's Attendance</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body" style="max-height: 342px;">
                        <canvas id="attendanceChart" style="width: 821px; height: 150px;"></canvas>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
        @endif
        @if($userRoleId == AppHelper::USER_STUDENT)
            <div class="row">
                <div class="col-md-3 col-md-offset-4">
                <div class="callout callout-success text-center">
                    <h3>Welcome to DevSuite Edu</h3>
                    <p>Lot's of things are coming soon...</p>
                </div>
                </div>
            </div>
        @endif

    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script src="{{asset(mix('js/dashboard.js'))}}"></script>
    <script type="text/javascript">
        @if($userRoleId != AppHelper::USER_STUDENT)
            window.attendanceLabel = @php echo json_encode(array_keys($attendanceChartPresentData)) @endphp;
            window.presentData = @php echo json_encode(array_values($attendanceChartPresentData)) @endphp;
            window.absentData = @php echo json_encode(array_values($attendanceChartAbsentData)) @endphp;
        @endif

        $(document).ready(function () {
            Dashboard.init();

        });

    </script>
@endsection
<!-- END PAGE JS-->
