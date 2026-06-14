@extends('backend.layouts.master')
@section('pageTitle') Fee Types @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Fee Types <small>@if($feeType) Update @else Add New @endif</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ URL::route('finance.fee_type.index') }}">Fee Types</a></li>
            <li class="active">@if($feeType) Update @else Add @endif</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-info">
                    <form novalidate id="entryForm" action="@if($feeType) {{ URL::route('finance.fee_type.update', $feeType->id) }} @else {{ URL::route('finance.fee_type.store') }} @endif" method="post">
                        @csrf
                        <div class="box-body">
                            <div class="form-group">
                                <label>Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="code" value="{{ old('code', $feeType->code ?? '') }}" required maxlength="30">
                            </div>
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $feeType->name ?? '') }}" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label>Billing Cycle <span class="text-danger">*</span></label>
                                <select name="billing_cycle" class="form-control select2" required>
                                    @foreach(AppHelper::BILLING_CYCLES as $key => $label)
                                        <option value="{{ $key }}" @if(old('billing_cycle', $feeType->billing_cycle ?? '') == $key) selected @endif>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Applies To <span class="text-danger">*</span></label>
                                <select name="applies_to" class="form-control select2" required>
                                    @foreach(AppHelper::FEE_APPLIES_TO as $key => $label)
                                        <option value="{{ $key }}" @if(old('applies_to', $feeType->applies_to ?? 'all') == $key) selected @endif>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="is_optional" value="1" @if(old('is_optional', $feeType->is_optional ?? 0)) checked @endif> Optional / Ad hoc fee</label>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a href="{{ URL::route('finance.fee_type.index') }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info pull-right"><i class="fa @if($feeType) fa-refresh @else fa-plus-circle @endif"></i> @if($feeType) Update @else Add @endif</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('extraScript')
    <script type="text/javascript">$(document).ready(function () { Generic.initCommonPageJS(); });</script>
@endsection
