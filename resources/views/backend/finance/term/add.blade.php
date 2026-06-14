@extends('backend.layouts.master')
@section('pageTitle') Academic Terms @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Academic Terms <small>@if($term) Update @else Add New @endif</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ URL::route('finance.term.index') }}">Terms</a></li>
            <li class="active">@if($term) Update @else Add @endif</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-info">
                    <form novalidate id="entryForm" action="@if($term) {{ URL::route('finance.term.update', $term->id) }} @else {{ URL::route('finance.term.store') }} @endif" method="post">
                        @csrf
                        <div class="box-body">
                            <div class="form-group">
                                <label>Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" class="form-control select2" required>
                                    <option value="">Select</option>
                                    @foreach($academicYears as $id => $title)
                                        <option value="{{ $id }}" @if(old('academic_year_id', $term->academic_year_id ?? '') == $id) selected @endif>{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $term->name ?? '') }}" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <input type="text" class="form-control datepicker" name="start_date" value="{{ old('start_date', isset($term->start_date) ? $term->start_date->format('d/m/Y') : '') }}" required>
                            </div>
                            <div class="form-group">
                                <label>End Date <span class="text-danger">*</span></label>
                                <input type="text" class="form-control datepicker" name="end_date" value="{{ old('end_date', isset($term->end_date) ? $term->end_date->format('d/m/Y') : '') }}" required>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a href="{{ URL::route('finance.term.index') }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info pull-right"><i class="fa @if($term) fa-refresh @else fa-plus-circle @endif"></i> @if($term) Update @else Add @endif</button>
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
