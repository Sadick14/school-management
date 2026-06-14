@extends('backend.layouts.master')
@section('pageTitle') Expense Chart @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Expenses <small>By Category</small></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        <form method="get" class="form-inline">
                            <input type="month" name="month" class="form-control" value="{{ $month }}" onchange="this.form.submit()">
                        </form>
                        <h4 class="margin-top-10">Total: {{ number_format($total, 2) }}</h4>
                    </div>
                    <div class="box-body">
                        <canvas id="expenseChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('extraScript')
    <script src="{{ asset(mix('js/dashboard.js')) }}"></script>
    <script type="text/javascript">
        window.expenseChartLabels = @json($labels);
        window.expenseChartValues = @json($values);
        $(document).ready(function () { Finance.expenseChartInit(); });
    </script>
    <script src="{{ asset(mix('js/finance.js')) }}"></script>
@endsection
