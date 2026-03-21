<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ __('sales.invoice') }} #{{ $sale->id }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 40px;
            color: #111827;
            font-size: 14px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-left {
            float: left;
        }
        .header-right {
            float: right;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .store-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #374151;
        }
        .info-label {
            font-size: 10px;
            font-weight: bold;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 13px;
            font-weight: 500;
        }
        .customer-section {
            margin-bottom: 30px;
        }
        .customer-name {
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #f9fafb;
            color: #6b7280;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #f3f4f6;
        }
        td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            float: right;
            width: 250px;
        }
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 4px 0;
            border-bottom: none;
            font-size: 13px;
        }
        .total-label {
            text-align: left;
            color: #6b7280;
        }
        .total-value {
            text-align: right;
            font-weight: bold;
        }
        .grand-total-row td {
            border-top: 2px solid #f3f4f6;
            padding-top: 12px;
            margin-top: 10px;
            color: #2563eb;
            font-size: 18px;
        }
        .footer {
            position: fixed;
            bottom: 40px;
            left: 0;
            right: 0;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
            border-top: 1px solid #f3f4f6;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <div class="store-name">{{ $settings->store_name ?? config('app.name') }}</div>
            <div class="invoice-title">{{ __('sales.invoice') }} #{{ $sale->id }}</div>
        </div>
        <div class="header-right">
            <div class="info-label">{{ __('sales.sale_info') }}</div>
            <div class="info-value"><span style="color: #6b7280;">{{ __('sales.date') }}:</span> {{ $sale->created_at->format('d/m/Y H:i') }}</div>
            <div class="info-value"><span style="color: #6b7280;">{{ __('sales.payment_method') }}:</span> {{ __("sales.payments.{$sale->payment_method}") }}</div>
            <div class="info-value"><span style="color: #6b7280;">{{ __('sales.status') }}:</span> {{ __("sales.{$sale->status->value}") }}</div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="customer-section">
        <div class="info-label">{{ __('sales.customer_info') }}</div>
        <div class="customer-name">{{ $sale->customer->name ?? __('sales.customer_placeholder') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('sales.product') }}</th>
                <th class="text-center">{{ __('sales.quantity') }}</th>
                <th class="text-right">{{ __('sales.unit_price') }}</th>
                <th class="text-right">{{ __('sales.subtotal') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: 600;">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table class="total-table">
            <tr>
                <td class="total-label">{{ __('sales.subtotal') }}</td>
                <td class="total-value">R$ {{ number_format($sale->items->sum('subtotal'), 2, ',', '.') }}</td>
            </tr>
            @if($sale->discount_amount > 0)
                <tr>
                    <td class="total-label">{{ __('sales.discount') }}</td>
                    <td class="total-value" style="color: #dc2626;">- R$ {{ number_format($sale->discount_amount, 2, ',', '.') }}</td>
                </tr>
            @endif
            @if($sale->fee_amount > 0)
                <tr>
                    <td class="total-label">{{ __('sales.fee') }} ({{ $sale->fee_percentage }}%)</td>
                    <td class="total-value">
                        {{ $sale->pass_fee_to_customer ? '+' : '-' }} R$ {{ number_format($sale->fee_amount, 2, ',', '.') }}
                    </td>
                </tr>
            @endif
            <tr class="grand-total-row">
                <td class="total-label" style="font-weight: bold;">{{ __('sales.total') }}</td>
                <td class="total-value">R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        {{ $settings->store_name ?? config('app.name') }} - {{ date('Y') }}
    </div>
</body>
</html>
