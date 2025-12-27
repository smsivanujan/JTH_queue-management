<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Report - {{ $monthStart->format('F Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 40px;
            background: white;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #1e40af;
        }
        .header .company {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }
        .report-info {
            margin-bottom: 30px;
            padding: 15px;
            background: #f3f4f6;
            border-radius: 5px;
        }
        .report-info strong {
            color: #1e40af;
        }
        .section {
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .metric-card {
            text-align: center;
            padding: 20px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .metric-value {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
        }
        .metric-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        table tfoot td {
            font-weight: bold;
            background: #f9fafb;
            border-top: 2px solid #d1d5db;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Monthly Usage Report</h1>
        <div class="company">
            <strong>{{ $tenant->name }}</strong><br>
            {{ $tenant->email }}<br>
            @if($tenant->phone)
                {{ $tenant->phone }}<br>
            @endif
            @if($tenant->address)
                {{ $tenant->address }}
            @endif
        </div>
    </div>

    <!-- Report Info -->
    <div class="report-info">
        <strong>Report Period:</strong> {{ $monthStart->format('F j, Y') }} to {{ $monthEnd->format('F j, Y') }}<br>
        <strong>Generated:</strong> {{ now()->format('F j, Y g:i A') }}
    </div>

    <!-- Usage Summary -->
    <div class="section">
        <div class="section-title">Usage Summary</div>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['clinics_count'] }}</div>
                <div class="metric-label">Clinics</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['services_count'] }}</div>
                <div class="metric-label">Services</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['queues_opened'] }}</div>
                <div class="metric-label">Queues Opened</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ number_format($metrics['tokens_served']) }}</div>
                <div class="metric-label">Tokens Served</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['screens_used'] }}</div>
                <div class="metric-label">Screens Used</div>
            </div>
        </div>
    </div>

    <!-- Billing Summary -->
    <div class="section">
        <div class="section-title">Billing Summary</div>
        @if($invoices->isEmpty())
            <p>No invoices found for this period.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>#{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->created_at->format('M j, Y') }}</td>
                            <td>${{ number_format($invoice->amount, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $invoice->isPaid() ? 'paid' : ($invoice->isPending() ? 'pending' : 'cancelled') }}">
                                    {{ $invoice->isPaid() ? 'Paid' : ($invoice->isPending() ? 'Pending' : 'Cancelled') }}
                                </span>
                            </td>
                            <td>{{ ucfirst($invoice->payment_method ?? 'N/A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td><strong>${{ number_format($invoices->sum('amount'), 2) }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Generated by SmartQueue Queue Management System</p>
        <p>This is an automatically generated report. For questions, please contact support.</p>
    </div>
</body>
</html>

