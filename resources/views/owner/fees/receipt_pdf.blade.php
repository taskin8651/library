<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1d23; margin: 0; padding: 20px; }
    .header { text-align: center; border-bottom: 2px solid #0d6efd; padding-bottom: 15px; margin-bottom: 15px; }
    .header h2 { margin: 0; font-size: 20px; color: #0d6efd; }
    .header p { margin: 3px 0; color: #666; font-size: 11px; }
    .badge { background: #0d6efd; color: white; padding: 4px 12px; border-radius: 4px; font-size: 13px; font-weight: bold; }
    .row { display: flex; justify-content: space-between; margin-bottom: 15px; }
    .member-box { background: #f4f6f9; border-radius: 8px; padding: 12px; margin-bottom: 15px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    td { padding: 6px 0; border-bottom: 1px solid #f0f0f0; }
    td:last-child { text-align: right; font-weight: 600; }
    .amount { color: #16a34a; font-size: 18px; font-weight: 700; }
    .footer { border-top: 1px solid #e9ecef; padding-top: 12px; display: flex; justify-content: space-between; margin-top: 20px; }
    .sig-line { border-top: 1px solid #000; width: 120px; margin-top: 40px; }
    .note { text-align: center; color: #999; font-size: 10px; margin-top: 20px; }
</style>
</head>
<body>
<div class="header">
    <h2>{{ $library->name }}</h2>
    @if($library->tagline)<p>{{ $library->tagline }}</p>@endif
    @if($library->address)<p>{{ $library->address }}</p>@endif
    <br><span class="badge">FEE RECEIPT</span>
</div>

<div class="row">
    <div>
        <strong>Receipt No:</strong> {{ $payment->receipt_number }}<br>
        <strong>Date:</strong> {{ $payment->payment_date->format('d M Y') }}
    </div>
    <div style="text-align:right">
        <strong>Collected By:</strong> {{ $payment->collected_by }}
    </div>
</div>

<div class="member-box">
    <strong>Member: {{ $payment->member->user->name }}</strong><br>
    UID: {{ $payment->member->uid }} &nbsp;|&nbsp;
    Seat: {{ $payment->member->seat?->seat_number ?? 'N/A' }} &nbsp;|&nbsp;
    Shift: {{ $payment->member->shift?->name ?? 'N/A' }}
</div>

<table>
    <tr><td>Amount Paid</td><td class="amount">₹{{ number_format($payment->amount) }}</td></tr>
    <tr><td>Payment Mode</td><td style="text-transform:uppercase">{{ $payment->payment_mode }}</td></tr>
    @if($payment->upi_ref)<tr><td>UPI Reference</td><td>{{ $payment->upi_ref }}</td></tr>@endif
    <tr><td>Valid From</td><td>{{ $payment->valid_from->format('d M Y') }}</td></tr>
    <tr><td>Valid Till</td><td style="color:#0d6efd">{{ $payment->valid_till->format('d M Y') }}</td></tr>
    @if($payment->notes)<tr><td>Notes</td><td>{{ $payment->notes }}</td></tr>@endif
</table>

<div class="footer">
    <div></div>
    <div style="text-align:right">
        <div>Authorized Signature</div>
        <div class="sig-line"></div>
    </div>
</div>

<div class="note">This is a computer-generated receipt. No signature required if stamped.</div>
</body>
</html>
