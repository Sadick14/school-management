@extends('backend.layouts.master')
@section('pageTitle') Expenses @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Expenses <small>List</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Expenses</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header border">
                        <form method="get" class="form-inline">
                            <input type="month" name="month" class="form-control" value="{{ $month }}">
                            <select name="category" class="form-control select2">
                                <option value="">All Categories</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}" @if($categoryId == $id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-info btn-sm">Filter</button>
                        </form>
                        <div class="box-tools pull-right">
                            <a class="btn btn-default btn-sm" href="{{ URL::route('finance.expense.chart') }}"><i class="fa fa-pie-chart"></i> Chart</a>
                            <a class="btn btn-add-new btn-sm" href="{{ URL::route('finance.expense.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        </div>
                    </div>
                    <div class="box-body margin-top-20">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr><th>Date</th><th>Category</th><th>Description</th><th>Reference</th><th>Amount</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                            @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->expense_date ? $expense->expense_date->format('d/m/Y') : '' }}</td>
                                    <td>{{ $expense->category ? $expense->category->name : '' }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ $expense->reference_no }}</td>
                                    <td>{{ number_format($expense->amount, 2) }}</td>
                                    <td>
                                        <a href="{{ URL::route('finance.expense.edit', $expense->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                        <form class="myAction" method="POST" action="{{ URL::route('finance.expense.destroy') }}">
                                            @csrf<input type="hidden" name="hiddenId" value="{{ $expense->id }}">
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No expenses found.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                        {{ $expenses->appends(['month' => $month, 'category' => $categoryId])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('extraScript')
    <script type="text/javascript">$(document).ready(function () { Generic.initCommonPageJS(); Generic.initDeleteDialog(); });</script>
@endsection
