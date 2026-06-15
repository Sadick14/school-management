@extends('backend.layouts.master')
@section('pageTitle') Payments @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Payments <small>Edit</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ URL::route('finance.payment.index') }}">Payments</a></li>
            <li class="active">Edit</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-info">
                    <div class="box-header border">
                        <h3 class="box-title">Receipt {{ $payment->receipt_no }}</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Student:</strong> {{ $payment->student->name ?? '' }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Class:</strong> {{ $payment->registration && $payment->registration->class ? $payment->registration->class->name : '' }}
                            </div>
                        </div>
                        <div class="row margin-top-10">
                            <div class="col-sm-6">
                                <strong>Date:</strong> {{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '' }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Total Amount:</strong> {{ number_format($payment->total_amount, 2) }}
                            </div>
                        </div>
                    </div>

                    @if($payment->items->isEmpty())
                        <div class="box-body">
                            <p class="text-muted">This payment has no fee items to reassign.</p>
                        </div>
                        <div class="box-footer">
                            <a href="{{ URL::route('finance.payment.index') }}" class="btn btn-default">Back</a>
                        </div>
                    @elseif($ledgers->isEmpty())
                        <div class="box-body">
                            <p class="text-muted">No fee ledger entries are available for this student to reassign to.</p>
                        </div>
                        <div class="box-footer">
                            <a href="{{ URL::route('finance.payment.index') }}" class="btn btn-default">Back</a>
                        </div>
                    @else
                        <form novalidate id="entryForm" action="{{ URL::route('finance.payment.update', $payment->id) }}" method="post">
                            @csrf
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Amount Paid</th>
                                        <th>Fee Type</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($payment->items as $item)
                                        <tr>
                                            <td class="va-middle">{{ number_format($item->amount_applied, 2) }}</td>
                                            <td>
                                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                                <select name="items[{{ $loop->index }}][student_ledger_id]" class="form-control select2" required>
                                                    @foreach($ledgers as $ledger)
                                                        <option value="{{ $ledger->id }}" @if($ledger->id == $item->student_ledger_id) selected @endif>
                                                            {{ $ledger->feeType ? $ledger->feeType->name : 'Unknown' }}@if($ledger->description) - {{ $ledger->description }} @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="box-footer">
                                <a href="{{ URL::route('finance.payment.index') }}" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-info pull-right"><i class="fa fa-save"></i> Save Changes</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
@section('extraScript')
    <script type="text/javascript">$(document).ready(function () { Generic.initCommonPageJS(); });</script>
@endsection
