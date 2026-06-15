@extends('backend.layouts.master')
@section('pageTitle') Academic Terms @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Academic Terms <small>List</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Terms</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header border">
                        <form method="get" class="form-inline">
                            <div class="form-group">
                                <select name="academic_year" id="academic_year_filter" class="form-control select2">
                                    <option value="">All Academic Years</option>
                                    @foreach($academicYears as $id => $title)
                                        <option value="{{ $id }}" @if($academicYearId == $id) selected @endif>{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                        <div class="box-tools pull-right">
                            <a class="btn btn-add-new btn-sm" href="{{ URL::route('finance.term.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        </div>
                    </div>
                    <div class="box-body margin-top-20">
                        <div class="table-responsive">
                            <table id="listDataTableOnlyPrint" class="table table-bordered table-striped list_view_table display responsive no-wrap" width="100%">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Academic Year</th>
                                    <th>Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Current Term</th>
                                    <th class="notexport">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($terms as $term)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $term->academicYear ? $term->academicYear->title : '' }}</td>
                                        <td>{{ $term->name }}</td>
                                        <td>{{ $term->start_date ? $term->start_date->format('d/m/Y') : '' }}</td>
                                        <td>{{ $term->end_date ? $term->end_date->format('d/m/Y') : '' }}</td>
                                        <td>{{ $term->status == AppHelper::ACTIVE ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            @if(($currentTermIds[$term->academic_year_id] ?? null) == $term->id)
                                                <span class="label label-success">Current</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a title="Edit" href="{{ URL::route('finance.term.edit', $term->id) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                            <form class="myAction" method="POST" action="{{ URL::route('finance.term.destroy') }}">
                                                @csrf
                                                <input type="hidden" name="hiddenId" value="{{ $term->id }}">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
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
    <script type="text/javascript">
        $(document).ready(function () {
            Generic.initCommonPageJS();
            Generic.initDeleteDialog();
            $('#academic_year_filter').on('change', function () {
                window.location = '{{ URL::route('finance.term.index') }}?academic_year=' + $(this).val();
            });
        });
    </script>
@endsection
