<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>System Report</title>
    <style>
        @page {
            margin: 0.5cm 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #2d3748;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            font-size: 12px;
        }
        .header { background: #1a202c; color: white; padding: 30px 40px; text-align: left; }
        .header h1 { margin: 0; font-size: 24px; letter-spacing: -0.025em; }
        .header p { margin: 5px 0 0 0; font-size: 13px; color: #a0aec0; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #718096;
            padding: 15px 0;
            border-top: 1px solid #e2e8f0;
            background: white;
        }
        .container { padding: 20px 40px; padding-bottom: 80px; }

        .grid { display: block; width: 100%; margin-bottom: 20px; clear: both; }
        .col-3 { float: left; width: 31%; margin-right: 2%; }
        .col-2 { float: left; width: 48.5%; margin-right: 2%; }
        .last { margin-right: 0; }

        .card { background: #ffffff; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .card h4 { margin: 0 0 8px 0; color: #718096; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
        .card .value { font-size: 18px; font-weight: bold; color: #1a202c; white-space: nowrap; }
        .card .sub-value { font-size: 11px; color: #718096; margin-top: 2px; }

        .clearfix { clear: both; }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 12px;
            color: #1a202c;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th { background: #f8fafc; text-align: left; padding: 10px 12px; font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
        td { padding: 10px 12px; font-size: 12px; border-bottom: 1px solid #f1f5f9; word-wrap: break-word; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }

        /* Prevent table rows from breaking across pages */
        tr { page-break-inside: avoid; }

        .text-right { text-align: right; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-blue { color: #2563eb; }
        .font-bold { font-weight: bold; }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            background: #f1f5f9;
            color: #475569;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('reports.pdf.executive_summary') }}</h1>
        <p>{{ __('reports.pdf.reporting_period') }}: <span style="color: white; font-weight: 600;">{{ $startDate }}</span> to <span style="color: white; font-weight: 600;">{{ $endDate }}</span></p>
    </div>

    <div class="container">
        <!-- Key Metrics -->
        <div class="grid">
            <div class="col-2 card">
                <h4>{{ __('reports.pdf.total_sales_gross') }}</h4>
                <div class="value">R$ {{ number_format($totalRevenue / 100, 2, ',', '.') }}</div>
                <div class="sub-value">{{ $salesByPaymentMethod->sum('count') }} {{ __('reports.pdf.transactions') }}</div>
            </div>
            <div class="col-2 card" style="margin-right: 0;">
                <h4>{{ __('reports.pdf.net_revenue') }}</h4>
                <div class="value text-blue">R$ {{ number_format($netSales / 100, 2, ',', '.') }}</div>
                <div class="sub-value">{{ __('reports.pdf.after_discounts_fees') }}</div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="grid" style="margin-top: 15px;">
            <div class="col-2 card">
                <h4>{{ __('reports.pdf.discounts_applied') }}</h4>
                <div class="value text-red">R$ {{ number_format($totalDiscount / 100, 2, ',', '.') }}</div>
            </div>
            <div class="col-2 card" style="margin-right: 0;">
                <h4>{{ __('reports.pdf.fees_absorbed') }}</h4>
                <div class="value text-red">R$ {{ number_format($totalFees / 100, 2, ',', '.') }}</div>
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- Sales by Payment Method -->
        <div class="section-title">{{ __('reports.pdf.sales_by_payment_method') }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">{{ __('reports.pdf.method') }}</th>
                    <th class="text-right" style="width: 20%;">{{ __('reports.pdf.transactions') }}</th>
                    <th class="text-right" style="width: 25%;">{{ __('reports.pdf.total_amount') }}</th>
                    <th class="text-right" style="width: 20%;">{{ __('reports.pdf.share') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesByPaymentMethod as $method => $data)
                    <tr>
                        <td><span class="badge">{{ ucfirst($method) }}</span></td>
                        <td class="text-right">{{ $data['count'] }}</td>
                        <td class="text-right font-bold">R$ {{ number_format($data['amount'] / 100, 2, ',', '.') }}</td>
                        <td class="text-right text-blue">{{ number_format(($data['amount'] / $totalRevenue) * 100, 1) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Top Products -->
        <div class="section-title">{{ __('reports.pdf.top_selling_products') }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%;">{{ __('reports.pdf.product') }}</th>
                    <th class="text-right" style="width: 15%;">{{ __('reports.pdf.units') }}</th>
                    <th class="text-right" style="width: 20%;">{{ __('reports.pdf.unit_price_avg') }}</th>
                    <th class="text-right" style="width: 25%;">{{ __('reports.pdf.revenue') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $item)
                    <tr>
                        <td class="font-bold">{{ $item->product->name }}</td>
                        <td class="text-right">{{ $item->total_quantity }}</td>
                        <td class="text-right">R$ {{ number_format(($item->total_revenue / $item->total_quantity) / 100, 2, ',', '.') }}</td>
                        <td class="text-right font-bold text-green">R$ {{ number_format($item->total_revenue / 100, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <div class="footer">
        {{ __('reports.pdf.generated_by') }} <strong>{{ config('app.name') }}</strong> &bull; {{ $generatedAt }} &bull; {{ __('reports.pdf.prepared_for') }} {{ $user->name }}
    </div>
</body>
</html>
