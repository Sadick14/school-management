@extends('backend.layouts.master')
@section('pageTitle') Collect Payment @endsection
@section('extraStyle')
    <style>
        .student-search-wrap { position: relative; }
        .student-search-results {
            position: absolute; top: 100%; left: 0; right: 0; z-index: 1000;
            background: #fff; border: 1px solid #ddd; border-top: none;
            max-height: 260px; overflow-y: auto; display: none;
        }
        .student-search-results .result-item {
            padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f4f4f4;
        }
        .student-search-results .result-item:hover,
        .student-search-results .result-item.active { background: #f5f5f5; }
        .student-search-results .result-item .res-name { font-weight: bold; }
        .student-search-results .result-item .res-meta { color: #999; font-size: 12px; }

        .selected-student-card {
            display: flex; align-items: center; justify-content: space-between;
            background: #f9f9f9; border: 1px solid #eee; border-radius: 4px;
            padding: 12px 15px; margin-bottom: 15px;
        }
        .selected-student-card .student-name { font-size: 16px; font-weight: bold; }
        .selected-student-card .student-meta { color: #777; }

        .fee-cards { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
        .fee-card {
            border: 2px solid #ddd; border-radius: 6px; padding: 10px 14px;
            min-width: 200px; cursor: pointer; background: #fff; transition: all .15s;
        }
        .fee-card:hover { border-color: #3c8dbc; }
        .fee-card.selected { border-color: #00a65a; background: #f0fff4; }
        .fee-card .fee-name { font-weight: bold; }
        .fee-card .fee-desc { color: #888; font-size: 12px; }
        .fee-card .fee-balance { font-size: 18px; margin-top: 4px; }
        .fee-card .fee-amount-row { margin-top: 8px; display: none; }
        .fee-card.selected .fee-amount-row { display: block; }
        .fee-card .fa-check-circle { color: #00a65a; float: right; display: none; }
        .fee-card.selected .fa-check-circle { display: inline; }

        .optional-fee-btns .btn { margin: 0 5px 5px 0; }

        .credit-note { color: #00a65a; font-weight: bold; }

        .payment-bar {
            position: sticky; bottom: 0; background: #fff; border-top: 2px solid #eee;
            padding: 12px 0; margin-top: 15px; display: flex; align-items: center; justify-content: space-between;
        }
        .payment-bar .total-amount { font-size: 20px; font-weight: bold; }

        .feeding-period { font-size: 12px; color: #777; }
    </style>
@endsection
@section('pageContent')
    <section class="content-header">
        <h1>Collect Payment</h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('user.dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ URL::route('finance.payment.index') }}">Payments</a></li>
            <li class="active">Collect</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group student-search-wrap">
                            <label>Search Student</label>
                            <input type="text" id="studentSearch" class="form-control" placeholder="Type student name or registration number..." autocomplete="off">
                            <div id="searchResults" class="student-search-results"></div>
                        </div>

                        <div id="studentPanel" style="display:none;">
                            <div class="selected-student-card">
                                <div>
                                    <div class="student-name" id="selStudentName"></div>
                                    <div class="student-meta" id="selStudentMeta"></div>
                                </div>
                                <button type="button" class="btn btn-default btn-sm" id="changeStudentBtn">
                                    <i class="fa fa-exchange"></i> Change Student
                                </button>
                            </div>

                            <h4>Outstanding Fees</h4>
                            <div id="feeCards" class="fee-cards"></div>
                            <div id="noDues" class="text-muted" style="display:none;">No outstanding fees for this student.</div>
                            <div id="creditNote" class="credit-note" style="display:none;"></div>

                            <div id="optionalFeesContainer" class="optional-fee-btns"></div>

                            <p class="feeding-period">
                                Feeding period:
                                <input type="text" id="feeding_from" class="datepicker" value="{{ date('01/m/Y') }}" style="width:90px;border:none;border-bottom:1px dashed #ccc;">
                                to
                                <input type="text" id="feeding_to" class="datepicker" value="{{ date('d/m/Y') }}" style="width:90px;border:none;border-bottom:1px dashed #ccc;">
                                <button type="button" id="recalcBtn" class="btn btn-link btn-xs">Recalculate</button>
                            </p>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment Date</label>
                                        <input type="text" id="payment_date" class="form-control datepicker" value="{{ date('d/m/Y') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment Method</label>
                                        <select id="payment_method" class="form-control select2">
                                            @foreach(AppHelper::PAYMENT_METHODS as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Paid By <small class="text-muted">(optional)</small></label>
                                        <input type="text" id="paid_by" class="form-control" placeholder="Payer name">
                                    </div>
                                </div>
                            </div>

                            <div class="payment-bar">
                                <div class="total-amount">Total: <span id="totalAmount">0.00</span></div>
                                <button type="button" class="btn btn-success btn-lg" id="collectPaymentBtn" disabled>
                                    <i class="fa fa-check"></i> Collect Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('extraScript')
    <script type="text/javascript">
        window.finance_search_students_url = '{{ URL::route('finance.payment.search_students') }}';
        window.finance_dues_url = '{{ URL::route('finance.payment.dues') }}';
        window.finance_store_url = '{{ URL::route('finance.payment.store') }}';
        window.finance_payment_list_url = '{{ URL::route('finance.payment.index') }}';
    </script>
    <script src="{{ asset(mix('js/finance.js')) }}"></script>
    <script type="text/javascript">$(document).ready(function () { Generic.initCommonPageJS(); Finance.quickPayInit(); });</script>
@endsection
