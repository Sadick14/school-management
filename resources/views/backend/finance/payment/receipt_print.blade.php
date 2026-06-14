<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $payment->receipt_no }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        .total-row td { font-weight: bold; }
        .meta { margin-bottom: 10px; }
        .meta span { display: inline-block; width: 48%; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $appSettings['institute_settings']['name'] ?? 'School' }}</h2>
        <p>{{ $appSettings['institute_settings']['address'] ?? '' }}</p>
        <h3>Payment Receipt</h3>
    </div>

    <div class="meta">
        <span><strong>Receipt No:</strong> {{ $payment->receipt_no }}</span>
        <span><strong>Date:</strong> {{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '' }}</span>
        <span><strong>Student:</strong> {{ $payment->student ? $payment->student->name : '' }}</span>
        <span><strong>Reg No:</strong> {{ $payment->registration ? $payment->registration->regi_no : '' }}</span>
        <span><strong>Class:</strong> {{ $payment->registration && $payment->registration->class ? $payment->registration->class->name : '' }}</span>
        <span><strong>Academic Year:</strong> {{ $payment->academicYear ? $payment->academicYear->title : '' }}</span>
        <span><strong>Payment Method:</strong> {{ AppHelper::PAYMENT_METHODS[$payment->payment_method] ?? $payment->payment_method }}</span>
        <span><strong>Paid By:</strong> {{ $payment->paid_by ?? 'N/A' }}</span>
    </div>

    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Fee Type</th>
            <th>Description</th>
            <th>Amount Paid</th>
        </tr>
        </thead>
        <tbody>
        @foreach($payment->items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->ledger && $item->ledger->feeType ? $item->ledger->feeType->name : '' }}</td>
                <td>{{ $item->ledger ? $item->ledger->description : '' }}</td>
                <td>{{ number_format($item->amount_applied, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" style="text-align:right;">Total</td>
            <td>{{ number_format($payment->total_amount, 2) }}</td>
        </tr>
        </tbody>
    </table>

    @if($payment->note)
        <p><strong>Note:</strong> {{ $payment->note }}</p>
    @endif

    <p style="margin-top:30px;">Received by: {{ $payment->creator ? $payment->creator->name : '' }}</p>
</body>
</html>
