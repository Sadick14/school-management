@extends('backend.layouts.master')
@section('pageTitle') Expenses @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Expenses <small>@if($expense) Update @else Add New @endif</small></h1>
    </section>
    <section class="content">
        <div class="row"><div class="col-md-8 col-md-offset-2">
            <div class="box box-info">
                <form action="@if($expense) {{ URL::route('finance.expense.update', $expense->id) }} @else {{ URL::route('finance.expense.store') }} @endif" method="post">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label>Category <span class="text-danger">*</span></label>
                            <select name="expense_category_id" class="form-control select2" required>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}" @if(old('expense_category_id', $expense->expense_category_id ?? '') == $id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control datepicker" name="expense_date" value="{{ old('expense_date', isset($expense->expense_date) ? $expense->expense_date->format('d/m/Y') : date('d/m/Y')) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" class="form-control" name="amount" value="{{ old('amount', $expense->amount ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="2">{{ old('description', $expense->description ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Reference No</label>
                            <input type="text" class="form-control" name="reference_no" value="{{ old('reference_no', $expense->reference_no ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control select2">
                                <option value="">Select</option>
                                @foreach(AppHelper::PAYMENT_METHODS as $key => $label)
                                    <option value="{{ $key }}" @if(old('payment_method', $expense->payment_method ?? '') == $key) selected @endif>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a href="{{ URL::route('finance.expense.index') }}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-info pull-right">Save</button>
                    </div>
                </form>
            </div>
        </div></div>
    </section>
@endsection
@section('extraScript')
    <script type="text/javascript">$(document).ready(function () { Generic.initCommonPageJS(); });</script>
@endsection
