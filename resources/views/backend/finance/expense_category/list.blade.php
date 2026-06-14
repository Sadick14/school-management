@extends('backend.layouts.master')
@section('pageTitle') Expense Categories @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Expense Categories <small>List</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Expense Categories</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header border">
                        <div class="box-tools pull-right">
                            <a class="btn btn-add-new btn-sm" href="{{ URL::route('finance.expense_category.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        </div>
                    </div>
                    <div class="box-body margin-top-20">
                        <table id="listDataTableOnlyPrint" class="table table-bordered table-striped list_view_table display responsive no-wrap" width="100%">
                            <thead><tr><th>#</th><th>Name</th><th>Status</th><th class="notexport">Action</th></tr></thead>
                            <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->status == AppHelper::ACTIVE ? 'Active' : 'Inactive' }}</td>
                                    <td>
                                        <a href="{{ URL::route('finance.expense_category.edit', $category->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                        <form class="myAction" method="POST" action="{{ URL::route('finance.expense_category.destroy') }}">
                                            @csrf<input type="hidden" name="hiddenId" value="{{ $category->id }}">
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
    </section>
@endsection
@section('extraScript')
    <script type="text/javascript">$(document).ready(function () { Generic.initCommonPageJS(); Generic.initDeleteDialog(); });</script>
@endsection
