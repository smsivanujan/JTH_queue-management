<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @media print {
            @page {
                margin: 1cm;
            }
            body {
                margin: 0;
            }
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #1f2937;
            background: #fff;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }
        .header-left h1 {
            font-size: 28px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }
        .header-left p {
            font-size: 12px;
            color: #6b7280;
        }
        .header-right {
            text-align: right;
        }
        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status.paid {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        .status.pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
        }
        .status.cancelled {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .info-box h3 {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }
        .info-box p {
            font-size: 13px;
            color: #111827;
            margin-bottom: 4px;
        }
        .info-box p.name {
            font-weight: 600;
            font-size: 14px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .detail-item label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .detail-item span {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        thead {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }
        th {
            padding: 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.5px;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
            color: #111827;
        }
        tbody tr:last-child td {
            border-bottom: none;
        }
        tfoot {
            background: #f9fafb;
            border-top: 2px solid #e5e7eb;
        }
        tfoot td {
            padding: 16px 12px;
            text-align: right;
            font-weight: 600;
        }
        .total-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            margin-right: 20px;
        }
        .total-amount {
            font-size: 20px;
            color: #111827;
        }
        .description-title {
            font-weight: 600;
            margin-bottom: 4px;
        }
        .description-meta {
            font-size: 11px;
            color: #6b7280;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }
        .message-box {
            margin-top: 30px;
            padding: 16px;
            border-radius: 8px;
            font-size: 12px;
        }
        .message-box.paid {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }
        .message-box.pending {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
        }
        .amount-right {
            text-align: right;
        }
        .amount-right .currency {
            font-size: 11px;
            color: #6b7280;
            display: block;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <h1>Invoice</h1>
            <p>Invoice #{{ $invoice->invoice_number }}</p>
        </div>
        <div class="header-right">
            <span class="status {{ $invoice->status }}">
                {{ ucfirst($invoice->status) }}
            </span>
        </div>
    </div>

    <!-- Company and Tenant Info -->
    <div class="info-section">
        <div class="info-box">
            <h3>From</h3>
            <p class="name">SmartQueue Inc.</p>
            <p>Queue Management System</p>
            <p>support@smartqueue.com</p>
        </div>
        <div class="info-box">
            <h3>To</h3>
            <p class="name">{{ $tenant->name }}</p>
            @if($tenant->email)
                <p>{{ $tenant->email }}</p>
            @endif
            @if($tenant->address)
                <p>{{ $tenant->address }}</p>
            @endif
        </div>
    </div>

    <!-- Invoice Details -->
    <div class="details-grid">
        <div class="detail-item">
            <label>Issue Date</label>
            <span>{{ $invoice->issued_at->format('F d, Y') }}</span>
        </div>
        @if($invoice->paid_at)
            <div class="detail-item">
                <label>Paid Date</label>
                <span>{{ $invoice->paid_at->format('F d, Y') }}</span>
            </div>
        @endif
        @if($invoice->payment_method)
            <div class="detail-item">
                <label>Payment Method</label>
                <span>{{ ucfirst($invoice->payment_method) }}</span>
            </div>
        @endif
    </div>

    <!-- Invoice Items -->
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="amount-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="description-title">{{ $invoice->metadata['plan_name'] ?? 'Subscription' }}</div>
                    @if(isset($invoice->metadata['billing_cycle']))
                        <div class="description-meta">{{ ucfirst($invoice->metadata['billing_cycle']) }} subscription</div>
                    @endif
                    @if(isset($invoice->metadata['subscription_starts_at']) && isset($invoice->metadata['subscription_ends_at']))
                        <div class="description-meta">
                            {{ \Carbon\Carbon::parse($invoice->metadata['subscription_starts_at'])->format('M d, Y') }} - 
                            {{ \Carbon\Carbon::parse($invoice->metadata['subscription_ends_at'])->format('M d, Y') }}
                        </div>
                    @endif
                </td>
                <td class="amount-right">
                    ${{ number_format($invoice->amount, 2) }}
                    <span class="currency">{{ $invoice->currency }}</span>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <span class="total-label">Total:</span>
                    <span class="total-amount">${{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</span>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer Message -->
    @if($invoice->status === 'paid')
        <div class="message-box paid">
            <strong>Payment Received</strong><br>
            This invoice has been paid. Thank you for your business!
        </div>
    @elseif($invoice->status === 'pending')
        <div class="message-box pending">
            <strong>Payment Pending</strong><br>
            This invoice is awaiting payment. Please complete payment to activate your subscription.
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This is an official invoice from SmartQueue Inc.</p>
        <p>For support, contact support@smartqueue.com</p>
    </div>

    <script>
        // Auto-print when page loads (optional - can be removed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

