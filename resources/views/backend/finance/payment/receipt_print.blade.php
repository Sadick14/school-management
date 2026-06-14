<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $payment->receipt_no }}</title>
    <style>
        @page { margin: 16px 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #333; }

        table { border-collapse: collapse; }

        .header-table { width: 100%; margin-bottom: 6px; }
        .header-table td { vertical-align: top; }
        .school-name { font-size: 15px; font-weight: bold; color: #1a3e6f; margin: 0; }
        .school-meta { font-size: 8.5px; color: #666; line-height: 1.5; margin: 3px 0 0; }

        .receipt-tag { text-align: right; white-space: nowrap; }
        .receipt-tag .title { font-size: 13px; font-weight: bold; color: #1a3e6f; letter-spacing: 1px; }
        .receipt-tag .badge {
            display: inline-block; margin-top: 4px; padding: 2px 8px;
            background: #2f855a; color: #fff; font-size: 9px; font-weight: bold; letter-spacing: 1px;
        }
        .receipt-tag .no { font-size: 10px; margin-top: 5px; }
        .receipt-tag .date { font-size: 9px; color: #666; margin-top: 1px; }

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

        .meta-table { width: 100%; margin-bottom: 10px; }
        .meta-table td { padding: 3px 4px; font-size: 10px; }
        .meta-table .label { display: block; color: #999; font-size: 8px; text-transform: uppercase; letter-spacing: .5px; }

        .note-box { margin-bottom: 10px; padding: 6px 8px; background: #fffaf0; border: 1px solid #f0e0c0; font-size: 9.5px; }

        .signatures { width: 100%; margin-top: 36px; }
        .signatures td { width: 50%; text-align: center; font-size: 9px; color: #555; padding-top: 4px; }
        .signatures .line { border-top: 1px solid #999; padding-top: 4px; }

        .footer { text-align: center; font-size: 8px; color: #aaa; margin-top: 18px; border-top: 1px dashed #ccc; padding-top: 6px; }
    </style>
</head>
<body>
    @php
        $institute = $appSettings['institute_settings'] ?? [];
    @endphp

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
            <td class="receipt-tag">
                <div class="title">PAYMENT RECEIPT</div>
                <div class="badge">PAID</div>
                <div class="no">No: {{ $payment->receipt_no }}</div>
                <div class="date">{{ $payment->payment_date ? $payment->payment_date->format('d M, Y') : '' }}</div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td width="55%">
                <span class="label">Student Name</span>
                <span class="value">{{ $payment->student->name ?? '' }}</span>
            </td>
            <td width="45%">
                <span class="label">Registration No</span>
                <span class="value">{{ $payment->registration->regi_no ?? '' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Class</span>
                <span class="value">
                    {{ $payment->registration && $payment->registration->class ? $payment->registration->class->name : '' }}
                    @if($payment->registration && $payment->registration->section)
                        - {{ $payment->registration->section->name }}
                    @endif
                </span>
            </td>
            <td>
                <span class="label">Academic Year</span>
                <span class="value">{{ $payment->academicYear->title ?? '' }}</span>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
        <tr>
            <th width="6%">#</th>
            <th width="34%">Fee Type</th>
            <th>Description</th>
            <th width="20%" class="amount">Amount (GHS)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($payment->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->ledger && $item->ledger->feeType ? $item->ledger->feeType->name : '' }}</td>
                <td>{{ $item->ledger ? $item->ledger->description : '' }}</td>
                <td class="amount">{{ number_format($item->amount_applied, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" style="text-align:right;">Total Paid</td>
            <td class="amount">{{ number_format($payment->total_amount, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <table class="meta-table">
        <tr>
            <td width="50%">
                <span class="label">Payment Method</span>
                {{ AppHelper::PAYMENT_METHODS[$payment->payment_method] ?? $payment->payment_method }}
            </td>
            <td width="50%">
                <span class="label">Paid By</span>
                {{ $payment->paid_by ?: 'N/A' }}
            </td>
        </tr>
    </table>

    @if($payment->note)
        <div class="note-box"><strong>Note:</strong> {{ $payment->note }}</div>
    @endif

    <table class="signatures">
        <tr>
            <td><div class="line">Received By{{ $payment->creator ? ' — ' . $payment->creator->name : '' }}</div></td>
            <td><div class="line">Parent / Guardian Signature</div></td>
        </tr>
    </table>

    <div class="footer">
        This is a computer-generated receipt &mdash; no signature required for validity.<br>
        Printed on {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
