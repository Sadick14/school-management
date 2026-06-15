<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment History</title>
    <style>
        @page { margin: 16px 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #333; }

        table { border-collapse: collapse; }

        .header-table { width: 100%; margin-bottom: 6px; }
        .header-table td { vertical-align: top; }
        .school-name { font-size: 15px; font-weight: bold; color: #1a3e6f; margin: 0; }
        .school-meta { font-size: 8.5px; color: #666; line-height: 1.5; margin: 3px 0 0; }

        .report-tag { text-align: right; white-space: nowrap; }
        .report-tag .title { font-size: 13px; font-weight: bold; color: #1a3e6f; letter-spacing: 1px; }
        .report-tag .date { font-size: 9px; color: #666; margin-top: 1px; }

        .divider { border-top: 2px solid #1a3e6f; margin: 6px 0 10px; }

        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 3px 4px; font-size: 10px; }
        .info-table .label { display: block; color: #999; font-size: 8px; text-transform: uppercase; letter-spacing: .5px; }
        .info-table .value { font-weight: bold; }

        table.items { width: 100%; margin-bottom: 8px; }
        table.items th, table.items td { border: 1px solid #d0d7de; padding: 5px 6px; font-size: 10px; }
        table.items th { background: #1a3e6f; color: #fff; text-align: left; font-weight: normal; }
        table.items td.amount, table.items th.amount { text-align: right; }
        table.items tr.total-row td { font-weight: bold; background: #eef2f7; }

        .no-payments { padding: 8px 0; color: #999; font-style: italic; }

        .student-block { margin-bottom: 18px; }
        .student-block.with-break { page-break-after: always; }

        .footer { text-align: center; font-size: 8px; color: #aaa; margin-top: 18px; border-top: 1px dashed #ccc; padding-top: 6px; }
    </style>
</head>
<body>
    @php
        $institute = $appSettings['institute_settings'] ?? [];
    @endphp

    @forelse($registrations as $registration)
        <div class="student-block @unless($loop->last) with-break @endunless">
            <table class="header-table">
                <tr>
                    <td>
                        <p class="school-name">{{ $institute['name'] ?? 'School' }}</p>
                        <p class="school-meta">
                            {{ $institute['address'] ?? '' }}
                            @if(!empty($institute['phone_no']) || !empty($institute['email']))
                                <br>
                                @if(!empty($institute['phone_no']))Tel: {{ $institute['phone_no'] }}@endif
                                @if(!empty($institute['phone_no']) && !empty($institute['email']))&nbsp;|&nbsp;@endif
                                @if(!empty($institute['email'])){{ $institute['email'] }}@endif
                            @endif
                        </p>
                    </td>
                    <td class="report-tag">
                        <div class="title">PAYMENT HISTORY</div>
                        <div class="date">Printed on {{ now()->format('d M, Y H:i') }}</div>
                    </td>
                </tr>
            </table>

            <div class="divider"></div>

            <table class="info-table">
                <tr>
                    <td width="55%">
                        <span class="label">Student Name</span>
                        <span class="value">{{ $registration->student->name ?? '' }}</span>
                    </td>
                    <td width="45%">
                        <span class="label">Registration No</span>
                        <span class="value">{{ $registration->regi_no }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Class</span>
                        <span class="value">
                            {{ $registration->class->name ?? '' }}
                            @if($registration->section)
                                - {{ $registration->section->name }}
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="label">Academic Year</span>
                        <span class="value">{{ $academicYear->title ?? '' }}</span>
                    </td>
                </tr>
            </table>

            @php $studentPayments = $payments->get($registration->id, collect()); @endphp

            @if($studentPayments->isEmpty())
                <div class="no-payments">No payments recorded.</div>
            @else
                <table class="items">
                    <thead>
                    <tr>
                        <th width="6%">#</th>
                        <th width="15%">Date</th>
                        <th width="20%">Receipt No</th>
                        <th width="29%">Fee Type(s)</th>
                        <th width="15%">Method</th>
                        <th width="15%" class="amount">Amount (GHS)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($studentPayments as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '' }}</td>
                            <td>{{ $payment->receipt_no }}</td>
                            <td>{{ $payment->items->pluck('ledger.feeType.name')->filter()->unique()->implode(', ') }}</td>
                            <td>{{ AppHelper::PAYMENT_METHODS[$payment->payment_method] ?? $payment->payment_method }}</td>
                            <td class="amount">{{ number_format($payment->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="5" style="text-align:right;">Total Paid</td>
                        <td class="amount">{{ number_format($studentPayments->sum('total_amount'), 2) }}</td>
                    </tr>
                    </tbody>
                </table>
            @endif

            <div class="footer">
                This is a computer-generated report.
            </div>
        </div>
    @empty
        <p>No students found for the selected class.</p>
    @endforelse
</body>
</html>
