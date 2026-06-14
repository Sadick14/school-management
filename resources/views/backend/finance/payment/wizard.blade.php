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

        /* Stepper */
        .payment-steps { display: flex; list-style: none; padding: 0; margin: 0 0 25px 0; }
        .payment-step { flex: 1; text-align: center; position: relative; color: #999; }
        .payment-step:not(:last-child)::after {
            content: ''; position: absolute; top: 16px; left: 50%; width: 100%; height: 2px;
            background: #ddd; z-index: 0;
        }
        .payment-step.done:not(:last-child)::after { background: #3c8dbc; }
        .payment-step .step-circle {
            width: 34px; height: 34px; border-radius: 50%; background: #ddd; color: #fff;
            line-height: 34px; margin: 0 auto 5px; position: relative; z-index: 1; font-weight: bold;
        }
        .payment-step.active .step-circle, .payment-step.done .step-circle { background: #3c8dbc; }
        .payment-step .step-label { font-size: 12px; }
        .payment-step.active .step-label { color: #3c8dbc; font-weight: bold; }

        /* Step panels */
        .step-panel { display: none; }
        .step-panel.active { display: block; }
        .step-actions {
            display: flex; align-items: center; justify-content: space-between;
            border-top: 1px solid #eee; padding-top: 15px; margin-top: 15px;
        }
        .step-actions .total-amount { font-size: 18px; font-weight: bold; }

        /* Term summary */
        .active-term-badge {
            display: inline-block; background: #3c8dbc; color: #fff;
            padding: 4px 12px; border-radius: 3px; font-size: 12px; margin-bottom: 10px;
        }
        .term-summary { display: flex; gap: 10px; margin: 0 0 15px 0; }
        .term-summary-box {
            flex: 1; border: 1px solid #eee; border-radius: 4px; padding: 10px 15px;
            background: #f9f9f9; text-align: center;
        }
        .term-summary-box .ts-label { color: #888; font-size: 12px; text-transform: uppercase; }
        .term-summary-box .ts-value { font-size: 20px; font-weight: bold; }

        /* Fee table */
        #feeRowsTable th, #feeRowsTable td { vertical-align: middle; }
        #feeRowsTable .pay-now-input { width: 120px; }

        .optional-fee-btns .btn { margin: 0 5px 5px 0; }

        .credit-note { color: #00a65a; font-weight: bold; }

        .feeding-period { font-size: 12px; color: #777; }

        /* Payment method buttons */
        .payment-method-btns { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px; }
        .payment-method-btn {
            border: 2px solid #ddd; border-radius: 6px; padding: 15px 25px; cursor: pointer;
            background: #fff; text-align: center; min-width: 130px; transition: all .15s;
        }
        .payment-method-btn:hover { border-color: #3c8dbc; }
        .payment-method-btn.selected { border-color: #00a65a; background: #f0fff4; }
        .payment-method-btn i { font-size: 22px; display: block; margin-bottom: 5px; }

        /* Review summary */
        .review-summary { background: #f9f9f9; border: 1px solid #eee; border-radius: 4px; padding: 5px 15px; margin-bottom: 15px; }
        .review-summary .review-row {
            display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;
        }
        .review-summary .review-row:last-child { border-bottom: none; font-weight: bold; font-size: 16px; }
    </style>
@endsection
@section('pageContent')
    @php
        $paymentMethodIcons = [
            'cash' => 'fa-money',
            'bank' => 'fa-bank',
            'mobile_money' => 'fa-mobile',
            'cheque' => 'fa-credit-card',
        ];
    @endphp
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
            <div class="col-md-10 col-md-offset-1">
                <div class=" box-primary">
                    <div class="box-body">
                        <ul class="payment-steps">
                            <li class="payment-step active" data-step="1">
                                <div class="step-circle">1</div>
                                <div class="step-label">Student</div>
                            </li>
                            <li class="payment-step" data-step="2">
                                <div class="step-circle">2</div>
                                <div class="step-label">Fee &amp; Amount</div>
                            </li>
                            <li class="payment-step" data-step="3">
                                <div class="step-circle">3</div>
                                <div class="step-label">Method &amp; Review</div>
                            </li>
                        </ul>

                        {{-- Step 1: Student --}}
                        <div class="step-panel active" id="stepPanel1" data-step="1">
                            <div class="form-group">
                                <label>Filter by Class <small class="text-muted">(optional)</small></label>
                                <select id="filterClass" class="form-control select2" style="max-width:300px;">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group student-search-wrap">
                                <label>Search Student</label>
                                <input type="text" id="studentSearch" class="form-control" placeholder="Type student name or registration number..." autocomplete="off">
                                <div id="searchResults" class="student-search-results"></div>
                            </div>

                            <div id="selectedStudentCard" class="selected-student-card" style="display:none;">
                                <div>
                                    <div class="student-name" id="selStudentName"></div>
                                    <div class="student-meta" id="selStudentMeta"></div>
                                </div>
                                <button type="button" class="btn btn-default btn-sm" id="changeStudentBtn">
                                    <i class="fa fa-exchange"></i> Change Student
                                </button>
                            </div>

                            <div id="termSummaryWrap" style="display:none;">
                                <span class="active-term-badge" id="activeTermBadge"></span>
                                <div class="term-summary">
                                    <div class="term-summary-box">
                                        <div class="ts-label">Expected</div>
                                        <div class="ts-value" id="sumExpected">0.00</div>
                                    </div>
                                    <div class="term-summary-box">
                                        <div class="ts-label">Total Paid</div>
                                        <div class="ts-value" id="sumPaid">0.00</div>
                                    </div>
                                    <div class="term-summary-box">
                                        <div class="ts-label">Outstanding</div>
                                        <div class="ts-value" id="sumOutstanding">0.00</div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-actions">
                                <span></span>
                                <button type="button" class="btn btn-primary" id="toStep2Btn" disabled>
                                    Next <i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Step 2: Fee & Amount --}}
                        <div class="step-panel" id="stepPanel2" data-step="2">
                            <p class="feeding-period">
                                Feeding period:
                                <input type="text" id="feeding_from" class="date_picker" value="{{ date('01/m/Y') }}" style="width:90px;border:none;border-bottom:1px dashed #ccc;">
                                to
                                <input type="text" id="feeding_to" class="date_picker" value="{{ date('d/m/Y') }}" style="width:90px;border:none;border-bottom:1px dashed #ccc;">
                                <button type="button" id="recalcBtn" class="btn btn-link btn-xs">Recalculate</button>
                            </p>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="feeRowsTable">
                                    <thead>
                                    <tr>
                                        <th>Fee Type</th>
                                        <th>Expected</th>
                                        <th>Paid</th>
                                        <th>Outstanding</th>
                                        <th>Pay Now (GHS)</th>
                                    </tr>
                                    </thead>
                                    <tbody id="feeRowsBody"></tbody>
                                </table>
                            </div>

                            <div id="noDues" class="text-muted" style="display:none;">No outstanding fees for this student.</div>
                            <div id="creditNote" class="credit-note" style="display:none;"></div>

                            <div id="optionalFeesContainer" class="optional-fee-btns"></div>

                            <div class="step-actions">
                                <button type="button" class="btn btn-default" id="backToStep1Btn">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                                <div class="total-amount">Total: GHS <span id="totalAmount">0.00</span></div>
                                <button type="button" class="btn btn-primary" id="toStep3Btn" disabled>
                                    Next <i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Step 3: Method & Review --}}
                        <div class="step-panel" id="stepPanel3" data-step="3">
                            <h4>Payment Method</h4>
                            <div class="payment-method-btns" id="paymentMethodButtons">
                                @foreach(AppHelper::PAYMENT_METHODS as $key => $label)
                                    <div class="payment-method-btn" data-method="{{ $key }}">
                                        <i class="fa {{ $paymentMethodIcons[$key] ?? 'fa-money' }}"></i>
                                        {{ $label }}
                                    </div>
                                @endforeach
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment Date</label>
                                        <input type="text" id="payment_date" class="form-control date_picker" value="{{ date('d/m/Y') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Paid By <small class="text-muted">(optional)</small></label>
                                        <input type="text" id="paid_by" class="form-control" placeholder="Payer name">
                                    </div>
                                </div>
                            </div>

                            <h4>Review</h4>
                            <div class="review-summary" id="reviewSummary">
                                <div class="review-row"><span>Student</span><span id="reviewStudent"></span></div>
                                <div class="review-row"><span>Active Term</span><span id="reviewTerm"></span></div>
                                <div class="review-row"><span>Payment Method</span><span id="reviewMethod"></span></div>
                                <div class="review-row"><span>Payment Date</span><span id="reviewDate"></span></div>
                                <div class="review-row"><span>Total Amount</span><span>GHS <span id="reviewTotal">0.00</span></span></div>
                            </div>

                            <div class="step-actions">
                                <button type="button" class="btn btn-default" id="backToStep2Btn">
                                    <i class="fa fa-arrow-left"></i> Back
                                </button>
                                <button type="button" class="btn btn-success btn-lg" id="collectPaymentBtn">
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
