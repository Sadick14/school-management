@extends('backend.layouts.master')
@section('pageTitle') Registration Pending Payment @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Registration Pending Payment</h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ URL::route('student.index') }}">Students</a></li>
            <li class="active">Pending Payment</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">⚠️ Registration Pending Payment</h3>
                    </div>
                    <div class="box-body">
                        <div class="callout callout-warning">
                            <h4>Important Notice</h4>
                            <p>This student registration is currently <strong>INACTIVE</strong>. The student cannot access any classes, marks, or other academic features until the registration fee is paid.</p>
                        </div>

                        <h4>Student Information</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%;">Student Name</th>
                                <td>{{ $registration->student->name }}</td>
                            </tr>
                            <tr>
                                <th>Registration Number</th>
                                <td>{{ $registration->regi_no }}</td>
                            </tr>
                            <tr>
                                <th>Class</th>
                                <td>{{ $registration->class->name }}</td>
                            </tr>
                            <tr>
                                <th>Academic Year</th>
                                <td>{{ $registration->acYear->title }}</td>
                            </tr>
                        </table>

                        <h4>Outstanding Fees</h4>
                        @if($dues->count())
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dues as $due)
                                        <tr>
                                            <td>{{ $due->description ?: $due->feeType->name }}</td>
                                            <td>GHS {{ number_format($due->amount, 2) }}</td>
                                            <td><strong>GHS {{ number_format($due->balance, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                    <tr class="active">
                                        <td colspan="2" style="text-align: right;"><strong>Total Due:</strong></td>
                                        <td><strong>GHS {{ number_format($dues->sum('balance'), 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No outstanding fees found.</p>
                        @endif

                        <hr>

                        <h4>Next Steps</h4>
                        <ol>
                            <li>Collect the registration fee from the student</li>
                            <li>Click the "Collect Payment" button below to record the payment</li>
                            <li>Once payment is recorded, the registration will be automatically activated</li>
                        </ol>

                        <hr>

                        <div class="form-group">
                            <a href="{{ URL::route('finance.payment.wizard') }}" class="btn btn-success btn-lg" target="_blank">
                                <i class="fa fa-money"></i> Collect Payment Now
                            </a>
                            <a href="{{ URL::route('student.index') }}" class="btn btn-default btn-lg">
                                <i class="fa fa-arrow-left"></i> Back to Students
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
