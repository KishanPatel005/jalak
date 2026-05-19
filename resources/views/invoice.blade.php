@php
    $status = $booking->status;
    $type = $type ?? 'auto'; // Force 'booking', 'delivery', or 'return'
    
    $docType = 'RENT INVOICE';
    $themeColor = '#3b82f6'; // Blue
    $themeBg = '#eff6ff';

    // Apply forced type or dynamic logic
    if ($type === 'return' || ($type === 'auto' && $status === 'finished')) {
        $docType = 'RETURN SETTLEMENT';
        $themeColor = '#059669'; // Emerald
        $themeBg = '#ecfdf5';
    } elseif ($type === 'delivery' || ($type === 'auto' && in_array($status, ['dispatched', 'packed']))) {
        $docType = 'DELIVERY RECEIPT';
        $themeColor = '#d97706'; // Amber
        $themeBg = '#fffbeb';
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $docType }} - {{ $booking->invoice_no }}</title>
    <style>
        @charset "UTF-8";
        * { font-family: 'DejaVu Sans', sans-serif !important; }
        body { color: #333; font-size: 11px; margin: 0; padding: 0; }
        .invoice-box { padding: 30px; border: 1px solid #eee; background: #fff; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table td { padding: 8px; vertical-align: top; }
        .header { border-bottom: 3px solid {{ $themeColor }}; padding-bottom: 20px; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; color: {{ $themeColor }}; text-transform: uppercase; letter-spacing: 2px; }
        .info-table td { width: 50%; }
        .details-table { margin-top: 20px; }
        .details-table th { background: #f8fafc; border-bottom: 2px solid {{ $themeColor }}; padding: 10px; font-weight: bold; text-align: left; text-transform: uppercase; font-size: 9px; }
        .details-table td { border-bottom: 1px solid #f1f5f9; padding: 10px; }
        .totals-section { margin-top: 30px; float: right; width: 300px; }
        .totals-table td { padding: 5px 0; }
        .grand-total { font-size: 16px; font-weight: bold; color: {{ $themeColor }}; border-top: 2px solid {{ $themeColor }}; padding-top: 10px; }
        .barcode { text-align: center; margin-top: 40px; clear: both; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; color: #94a3b8; font-size: 9px; padding: 20px 0; border-top: 1px solid #f1f5f9; }
        .doc-badge { display: inline-block; padding: 4px 10px; background: {{ $themeBg }}; color: {{ $themeColor }}; font-weight: bold; font-size: 10px; border-radius: 4px; margin-top: 10px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <table>
                <tr>
                    <td>
                        <div class="title">{{ $docType }}</div>
                        <div style="margin-top: 5px; color: #64748b;">
                            Date: {{ date('d M Y') }}<br>
                            Invoice #: {{ $booking->invoice_no }}
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <div style="font-weight: bold; font-size: 14px;">JALAK FASHION</div>
                        <div style="color: #64748b; font-size: 10px;">
                            jalak fashion, dholka<br>
                            Phone: +91 90676 00673
                        </div>
                        <div class="doc-badge">{{ $status }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-table">
            <tr>
                <td>
                    <div style="font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 5px;">Billed To:</div>
                    <div style="font-size: 14px; font-weight: bold;">{{ $booking->customer->name }}</div>
                    <div>{{ $booking->customer->mobile }}</div>
                    @if($booking->customer->address)
                        <div style="margin-top: 5px; color: #475569;">{{ $booking->customer->address }}</div>
                    @endif
                </td>
                <td style="text-align: right;">
                    <div style="font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 5px;">Status:</div>
                    <div style="font-weight: bold; color: {{ $themeColor }}; text-transform: uppercase; font-size: 14px;">
                        {{ $status }}
                    </div>
                </td>
            </tr>
        </table>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Booking Dates</th>
                    <th style="text-align: center;">Days</th>
                    <th style="text-align: right;">Rent Calc</th>
                    <th style="text-align: right;">Rent Total</th>
                    <th style="text-align: right;">Deposit</th>
                    <th style="text-align: right;">Item Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalItemsRent = 0; $totalItemsDeposit = 0; @endphp
                @foreach($booking->items as $item)
                    @php 
                        $start = \Carbon\Carbon::parse($item->from_date);
                        $end = \Carbon\Carbon::parse($item->to_date);
                        $days = $start->diffInDays($end) + 1;
                        $itemRent = $days * $item->rent_price;
                        $totalItemsRent += $itemRent;
                        $totalItemsDeposit += $item->deposit_amount;
                    @endphp
                    <tr>
                        <td style="width: 20%;">
                            <div style="font-weight: bold;">{{ $item->product->name }}</div>
                            <div style="font-size: 8px; color: #64748b;">{{ $item->product->code }} | SIZE: {{ $item->size }}</div>
                        </td>
                        <td style="width: 25%;">{{ $item->from_date }} to {{ $item->to_date }}</td>
                        <td style="text-align: center; width: 8%;">{{ $days }}</td>
                        <td style="text-align: right; width: 15%; font-size: 10px;">{{ $days }} * <span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($item->rent_price, 2) }}</td>
                        <td style="text-align: right; width: 12%; font-weight: bold;"><span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($itemRent, 2) }}</td>
                        <td style="text-align: right; width: 10%;"><span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($item->deposit_amount, 2) }}</td>
                        <td style="text-align: right; width: 10%; font-weight: bold;"><span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($itemRent + $item->deposit_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Total Rent:</td>
                    <td style="text-align: right;"><span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($totalItemsRent, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Security Deposit:</td>
                    <td style="text-align: right;"><span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($totalItemsDeposit, 2) }}</td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td style="text-align: right; color: #dc2626;">- <span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($booking->discount, 2) }}</td>
                </tr>
                <tr>
                    <td class="grand-total">Grand Total:</td>
                    <td class="grand-total" style="text-align: right;"><span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($booking->grand_total, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding-top: 15px; font-weight: bold; font-size: 11px;">
                        Booking Advance:
                        <div style="font-size: 8px; font-weight: normal; color: #64748b;">{{ $booking->created_at->format('d M y - h:i A') }}</div>
                    </td>
                    <td style="text-align: right; padding-top: 15px; font-weight: bold; color: #059669;">
                        <span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($booking->advance_paid, 2) }}
                    </td>
                </tr>
                @if($booking->dispatch_paid > 0)
                <tr>
                    <td style="font-weight: bold; font-size: 11px;">
                        Delivery Payment:
                        <div style="font-size: 8px; font-weight: normal; color: #64748b;">{{ \Carbon\Carbon::parse($booking->dispatch_paid_at)->format('d M y - h:i A') }}</div>
                    </td>
                    <td style="text-align: right; font-weight: bold; color: {{ $themeColor }};">
                        <span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($booking->dispatch_paid, 2) }}
                    </td>
                </tr>
                @endif
                <tr style="border-top: 1px double #1e293b;">
                    <td style="font-weight: bold; color: #1e293b; font-size: 14px; padding-top: 10px;">Balance Amount:</td>
                    <td style="text-align: right; font-weight: bold; color: #1e293b; font-size: 14px; padding-top: 10px;">
                        <span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($booking->balance_to_pay, 2) }}
                    </td>
                </tr>

                @if($booking->status == 'finished')
                @php
                    $totalFine = $booking->items->sum('fine_amount');
                    $totalRefund = $booking->items->sum('deposit_refunded');
                    $returnDate = $booking->items->max('returned_at');
                @endphp
                <tr>
                    <td colspan="2" style="padding-top: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                        <span style="font-size: 10px; font-weight: bold; color: #1e293b; text-transform: uppercase;">Return Settlement Summary</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 10px; font-weight: bold; font-size: 11px;">
                        Total Fines / Deductions:
                        <div style="font-size: 8px; font-weight: normal; color: #64748b;">Loss/Damage/Late Fees</div>
                    </td>
                    <td style="text-align: right; padding-top: 10px; font-weight: bold; color: #dc2626;">
                        <span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($totalFine, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 12px; color: {{ $themeColor }};">
                        Final Refund to Customer:
                        <div style="font-size: 8px; font-weight: normal; color: #64748b;">Settled on {{ \Carbon\Carbon::parse($returnDate)->format('d M y - h:i A') }}</div>
                    </td>
                    <td style="text-align: right; font-weight: bold; font-size: 16px; color: {{ $themeColor }};">
                        <span style="font-family: 'DejaVu Sans'; font-weight: bold;">&#8377;</span>{{ number_format($totalRefund, 2) }}
                    </td>
                </tr>
                @endif
            </table>
        </div>

        @if($booking->note)
            <div style="margin-top: 40px; border-top: 1px dashed #e2e8f0; padding-top: 15px; width: 60%;">
                <div style="font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 5px; font-size: 9px;">Special Notes:</div>
                <div style="color: #475569;">{{ $booking->note }}</div>
            </div>
        @endif

        <div class="barcode">
            <img src="data:image/png;base64,{{ \Milon\Barcode\Facades\DNS1DFacade::getBarcodePNG($booking->invoice_no, 'C128') }}" alt="barcode" style="width: 250px; height: 50px;" />
            <div style="margin-top: 5px; font-weight: bold; font-size: 10px;">{{ $booking->invoice_no }}</div>
        </div>

        <div class="footer">
            Thank you for your business! This is a system-generated invoice.<br>
            Please keep the security deposit slip for the refund at the time of return.
        </div>
    </div>
</body>
</html>
