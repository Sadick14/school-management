@extends('backend.layouts.master')
@section('pageTitle') Fee Structure @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Fee Structure <small>List</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Fee Structure</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header border">
                        <form method="get" class="form-inline">
                            <select name="academic_year" id="academic_year_filter" class="form-control select2">
                                <option value="">All Years</option>
                                @foreach($academicYears as $id => $title)
                                    <option value="{{ $id }}" @if($academicYearId == $id) selected @endif>{{ $title }}</option>
                                @endforeach
                            </select>
                            <select name="class" id="class_filter" class="form-control select2">
                                <option value="">All Classes</option>
                                @foreach($classes as $id => $name)
                                    <option value="{{ $id }}" @if($classId == $id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                            <select name="term" id="term_filter" class="form-control select2">
                                <option value="">All Terms</option>
                                @foreach($terms as $id => $name)
                                    <option value="{{ $id }}" @if($termId == $id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-info btn-sm">Filter</button>
                        </form>
                        <div class="box-tools pull-right">
                            <a class="btn btn-add-new btn-sm" href="{{ URL::route('finance.fee_structure.create', ['academic_year' => $academicYearId]) }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        </div>
                    </div>
                    <div class="box-body margin-top-20">
                        <div class="table-responsive">
                            <table id="listDataTableOnlyPrint" class="table table-bordered table-striped list_view_table display responsive no-wrap" width="100%">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Academic Year</th>
                                    <th>Fee Type</th>
                                    <th>Class</th>
                                    <th>Term</th>
                                    <th>Amount</th>
                                    <th class="notexport">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($structures as $structure)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $structure->academicYear ? $structure->academicYear->title : '' }}</td>
                                        <td>{{ $structure->feeType ? $structure->feeType->name : '' }}</td>
                                        <td>{{ $structure->class ? $structure->class->name : 'All Classes' }}</td>
                                        <td>{{ $structure->term ? $structure->term->name : 'N/A' }}</td>
                                        <td>{{ number_format($structure->amount, 2) }}</td>
                                        <td>
                                            <a href="{{ URL::route('finance.fee_structure.edit', $structure->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                            <form class="myAction" method="POST" action="{{ URL::route('finance.fee_structure.destroy') }}">
                                                @csrf
                                                <input type="hidden" name="hiddenId" value="{{ $structure->id }}">
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
