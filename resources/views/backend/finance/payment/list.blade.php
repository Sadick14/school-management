@extends('backend.layouts.master')
@section('pageTitle') Payments @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Payments <small>List</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Payments</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header border">
                        <form method="get" class="form-inline">
                            <select name="academic_year" class="form-control select2 academic-year-select" onchange="this.form.submit()">
                                @foreach($academicYears as $id => $title)
                                    <option value="{{ $id }}" @if($academicYearId == $id) selected @endif>{{ $title }}</option>
                                @endforeach
                            </select>
                        </form>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#bulkRecordPaymentsModal">
                                <i class="fa fa-history"></i> Record Past Payments
                            </button>
                            <a class="btn btn-add-new btn-sm" href="{{ URL::route('finance.payment.wizard') }}"><i class="fa fa-money"></i> Collect Payment</a>
                        </div>
                    </div>
                    <div class="box-body margin-top-20">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Fee Type(s)</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->receipt_no }}</td>
                                        <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '' }}</td>
                                        <td>
                                            @if($payment->student)
                                                <div class="avatar-name-cell">
                                                    <img src="@if($payment->student->photo){{ asset('storage/student')}}/{{ $payment->student->photo }} @else {{ asset('images/avatar.jpg')}} @endif" alt="">
                                                    <div class="avatar-name-info">
                                                        <span class="avatar-name-title">{{ $payment->student->name }}</span>
                                                        <span class="avatar-name-subtitle">{{ $payment->receipt_no }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $payment->registration && $payment->registration->class ? $payment->registration->class->name : '' }}</td>
                                        <td>
                                            @foreach($payment->items->pluck('ledger.feeType.name')->filter()->unique() as $feeTypeName)
                                                <span class="badge bg-blue">{{ $feeTypeName }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ number_format($payment->total_amount, 2) }}</td>
                                        <td>{{ AppHelper::PAYMENT_METHODS[$payment->payment_method] ?? $payment->payment_method }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a target="_blank" href="{{ URL::route('finance.payment.receipt', $payment->id) }}" class="btn btn-info btn-sm" title="View Receipt"><i class="fa fa-eye"></i></a>
                                                <a href="{{ URL::route('finance.payment.receipt', $payment->id) }}?download=1" class="btn btn-default btn-sm" title="Download Receipt"><i class="fa fa-download"></i></a>
                                                <a href="{{ URL::route('finance.payment.edit', $payment->id) }}" class="btn btn-warning btn-sm" title="Edit Fee Type"><i class="fa fa-edit"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center">No payments found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $payments->appends(['academic_year' => $academicYearId])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('backend.finance.payment.bulk-record-modal')
@endsection

@section('extraScript')
    <script type="text/javascript">$(document).ready(function () { Generic.initCommonPageJS(); });</script>
    @include('backend.finance.payment.bulk-record-modal-script')
@endsection
