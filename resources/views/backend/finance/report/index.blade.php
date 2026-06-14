@extends('backend.layouts.master')
@section('pageTitle') Finance Reports @endsection
@section('pageContent')
    <section class="content-header">
        <h1>Finance Reports</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <form method="get" class="form-inline margin-bottom-15">
                    <select name="academic_year" class="form-control select2">
                        @foreach($academicYears as $id => $title)
                            <option value="{{ $id }}" @if($academicYearId == $id) selected @endif>{{ $title }}</option>
                        @endforeach
                    </select>
                    <input type="month" name="month" class="form-control" value="{{ $month }}">
                    <button type="submit" class="btn btn-info">Filter</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="modern-stat-card stat-color-teal">
                    <div class="stat-content"><h3>{{ number_format($revenueMonth, 2) }}</h3><p>Revenue (Month)</p></div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="modern-stat-card stat-color-pink">
                    <div class="stat-content"><h3>{{ number_format($expensesMonth, 2) }}</h3><p>Expenses (Month)</p></div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="modern-stat-card stat-color-purple">
                    <div class="stat-content"><h3>{{ number_format($revenueMonth - $expensesMonth, 2) }}</h3><p>Net (Month)</p></div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="modern-stat-card stat-color-orange">
                    <div class="stat-content"><h3>{{ number_format($outstandingArrears, 2) }}</h3><p>Outstanding Arrears</p></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="box box-info">
                    <div class="box-header"><h3>Revenue vs Expenses (Monthly)</h3></div>
                    <div class="box-body"><canvas id="financeTrendChart" height="100"></canvas></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box box-info">
                    <div class="box-header"><h3>Expenses by Category</h3></div>
                    <div class="box-body"><canvas id="financeCategoryChart" height="200"></canvas></div>
                </div>
            </div>
        </div>
        @if($totalCredit > 0)
            <div class="callout callout-info">Total student credit balance: {{ number_format($totalCredit, 2) }}</div>
        @endif
    </section>
@endsection
@section('extraScript')
    <script src="{{ asset(mix('js/dashboard.js')) }}"></script>
    <script type="text/javascript">
        window.financeTrendLabels = @json($monthlyRevenue->pluck('month'));
        window.financeRevenueData = @json($monthlyRevenue->pluck('total'));
        window.financeExpenseData = @json($monthlyExpenses->pluck('total'));
        window.financeCategoryLabels = @json($expenseByCategory->map(function($r){ return $r->category ? $r->category->name : 'Unknown'; }));
        window.financeCategoryData = @json($expenseByCategory->pluck('total'));
        $(document).ready(function () { Finance.reportInit(); });
    </script>
    <script src="{{ asset(mix('js/finance.js')) }}"></script>
@endsection
