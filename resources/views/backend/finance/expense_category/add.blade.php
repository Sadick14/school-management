@extends('backend.layouts.master')
@section('pageTitle') Expense Categories @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Expense Categories <small>@if($category) Update @else Add New @endif</small></h1>
    </section>
    <section class="content">
        <div class="row"><div class="col-md-6 col-md-offset-3">
            <div class="box box-info">
                <form action="@if($category) {{ URL::route('finance.expense_category.update', $category->id) }} @else {{ URL::route('finance.expense_category.store') }} @endif" method="post">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $category->name ?? '') }}" required>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a href="{{ URL::route('finance.expense_category.index') }}" class="btn btn-default">Cancel</a>
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
