@extends('backend.layouts.master')
@section('pageTitle') Fee Types @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Fee Types <small>List</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Fee Types</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header border">
                        <div class="box-tools pull-right">
                            <a class="btn btn-add-new btn-sm" href="{{ URL::route('finance.fee_type.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        </div>
                    </div>
                    <div class="box-body margin-top-20">
                        <div class="table-responsive">
                            <table id="listDataTableOnlyPrint" class="table table-bordered table-striped list_view_table display responsive no-wrap" width="100%">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Billing Cycle</th>
                                    <th>Applies To</th>
                                    <th>Optional</th>
                                    <th>Status</th>
                                    <th class="notexport">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($feeTypes as $feeType)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $feeType->code }}</td>
                                        <td>{{ $feeType->name }}</td>
                                        <td>{{ AppHelper::BILLING_CYCLES[$feeType->billing_cycle] ?? $feeType->billing_cycle }}</td>
                                        <td>{{ AppHelper::FEE_APPLIES_TO[$feeType->applies_to] ?? $feeType->applies_to }}</td>
                                        <td>{{ $feeType->is_optional ? 'Yes' : 'No' }}</td>
                                        <td>{{ $feeType->status == AppHelper::ACTIVE ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            <a href="{{ URL::route('finance.fee_type.edit', $feeType->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                            <form class="myAction" method="POST" action="{{ URL::route('finance.fee_type.destroy') }}">
                                                @csrf
                                                <input type="hidden" name="hiddenId" value="{{ $feeType->id }}">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('extraScript')
    <script type="text/javascript">$(document).ready(function () { Generic.initCommonPageJS(); Generic.initDeleteDialog(); });</script>
@endsection
