@extends('backend.layouts.master')
@section('pageTitle') Fee Structure @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Fee Structure <small>@if($structure) Update @else Add New @endif</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ URL::route('finance.fee_structure.index') }}">Fee Structure</a></li>
            <li class="active">@if($structure) Update @else Add @endif</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-info">
                    <form novalidate id="entryForm" action="@if($structure) {{ URL::route('finance.fee_structure.update', $structure->id) }} @else {{ URL::route('finance.fee_structure.store') }} @endif" method="post">
                        @csrf
                        <div class="box-body">
                            <div class="form-group">
                                <label>Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control select2" required>
                                    @foreach($academicYears as $id => $title)
                                        <option value="{{ $id }}" @if(old('academic_year_id', $structure->academic_year_id ?? $selectedYear) == $id) selected @endif>{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fee Type <span class="text-danger">*</span></label>
                                <select name="fee_type_id" class="form-control select2" required>
                                    @foreach($feeTypes as $id => $name)
                                        <option value="{{ $id }}" @if(old('fee_type_id', $structure->fee_type_id ?? '') == $id) selected @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Class</label>
                                <select name="class_id" class="form-control select2">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $id => $name)
                                        <option value="{{ $id }}" @if(old('class_id', $structure->class_id ?? '') == $id) selected @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Term</label>
                                <select name="term_id" id="term_id" class="form-control select2">
                                    <option value="">N/A</option>
                                    @foreach($terms as $id => $name)
                                        <option value="{{ $id }}" @if(old('term_id', $structure->term_id ?? '') == $id) selected @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" class="form-control" name="amount" value="{{ old('amount', $structure->amount ?? '') }}" required>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a href="{{ URL::route('finance.fee_structure.index') }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info pull-right"><i class="fa @if($structure) fa-refresh @else fa-plus-circle @endif"></i> @if($structure) Update @else Add @endif</button>
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
