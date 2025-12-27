<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Platform Business Report - {{ $monthStart->format('F Y') }}</title>
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
            padding: 25px 15px;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
        }
        .metric-value {
            font-size: 36px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .metric-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        .metric-description {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 5px;
        }
        .conversion-box {
            margin-top: 30px;
            padding: 20px;
            background: #f3f4f6;
            border-radius: 8px;
            border: 1px solid #d1d5db;
        }
        .conversion-box h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #374151;
        }
        .conversion-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .conversion-item {
            padding: 15px;
            background: white;
            border-radius: 5px;
        }
        .conversion-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .conversion-value {
            font-size: 28px;
            font-weight: bold;
            color: #1e40af;
        }
        .conversion-detail {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 5px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Platform Business Report</h1>
        <div style="margin-top: 10px; color: #666; font-size: 14px;">
            SmartQueue Platform Administration
        </div>
    </div>

    <!-- Report Info -->
    <div class="report-info">
        <strong>Report Period:</strong> {{ $monthStart->format('F j, Y') }} to {{ $monthEnd->format('F j, Y') }}<br>
        <strong>Generated:</strong> {{ now()->format('F j, Y g:i A') }}
    </div>

    <!-- Business Summary -->
    <div class="section">
        <div class="section-title">Monthly Business Metrics</div>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['total_tenants'] }}</div>
                <div class="metric-label">Total Tenants</div>
                <div class="metric-description">As of month end</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['active_tenants'] }}</div>
                <div class="metric-label">Active Tenants</div>
                <div class="metric-description">With active subscriptions</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['new_tenants'] }}</div>
                <div class="metric-label">New Tenants</div>
                <div class="metric-description">Created this month</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $metrics['trial_conversions'] }}</div>
                <div class="metric-label">Trial Conversions</div>
                <div class="metric-description">Trial → Paid</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">${{ number_format($metrics['mrr'], 0) }}</div>
                <div class="metric-label">Monthly MRR</div>
                <div class="metric-description">Recurring revenue</div>
            </div>
        </div>

        <!-- Conversion Metrics -->
        @if($metrics['total_tenants'] > 0)
            <div class="conversion-box">
                <h3>Conversion Metrics</h3>
                <div class="conversion-grid">
                    <div class="conversion-item">
                        <div class="conversion-label">Active Rate</div>
                        <div class="conversion-value">
                            {{ $metrics['total_tenants'] > 0 ? number_format(($metrics['active_tenants'] / $metrics['total_tenants']) * 100, 1) : 0 }}%
                        </div>
                        <div class="conversion-detail">{{ $metrics['active_tenants'] }} of {{ $metrics['total_tenants'] }} tenants</div>
                    </div>
                    @if($metrics['new_tenants'] > 0)
                        <div class="conversion-item">
                            <div class="conversion-label">Conversion Rate (New Tenants)</div>
                            <div class="conversion-value">
                                {{ number_format(($metrics['trial_conversions'] / $metrics['new_tenants']) * 100, 1) }}%
                            </div>
                            <div class="conversion-detail">{{ $metrics['trial_conversions'] }} conversions from {{ $metrics['new_tenants'] }} new tenants</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Generated by SmartQueue Queue Management System - Platform Administration</p>
        <p>This is an automatically generated confidential business report. For internal use only.</p>
    </div>
</body>
</html>

