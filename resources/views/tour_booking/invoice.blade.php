<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Tax Invoice {{ $invoice_mst->invoice_no . ' - ' . $invoice_mst->PartyName }}</title>
    <style type="text/css">
        @page {
            margin-top: 0.5cm;
            margin-bottom: 0.5cm;
            margin-left: 0.4cm;
            margin-right: 0.4cm;
        }

        body {
            background: #fff;
            color: #000;
            font-family: 'Open Sans', 'Arial', 'Helvetica', sans-serif;
            font-size: 10pt;
            line-height: 100%;
        }

        .paid-invoice-img {
            position: absolute;
            top: 0;
            right: 0;
            margin-bottom: 20px;
            z-index: 9999;
            float: right;
        }

        table {
            border-collapse: collapse;
        }

        hr {
            border: none;
            border-top: 1px solid #ccc;
        }
    </style>
</head>

<body>

    {{-- Paid / Unpaid stamp --}}
    @if ($balance > 0)
        <img align="right" src="{{ asset('assets/images/unpaid-invoice.png') }}" alt="" class="paid-invoice-img">
    @else
        <img align="right" src="{{ asset('assets/images/paid-invoice.png') }}" alt="" class="paid-invoice-img">
    @endif

    {{-- ── HEADER: Logo + TAX INVOICE title ── --}}
    <table width="100%" border="0" style="margin-top:0px;">
        <tr>
            <th width="50%" align="left" style="vertical-align:top;">
                <img src="{{ asset('documents/' . $company->Logo) }}" alt="">
            </th>
            <th width="50%">&nbsp;</th>
        </tr>
        <tr>
            <td width="50%" style="line-height:12pt">
                @include('components.company-details')
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td width="50%" align="right" style="font-size:28pt; font-weight:bolder;">
                <br><br>TAX INVOICE
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <div align="right" style="margin-top:15px;">
                    <strong>Balance Due<br>AED {{ number_format($balance, 2) }}</strong>
                </div><br>
            </td>
        </tr>
    </table>

    {{-- ── BILL TO + INVOICE DETAILS ── --}}
    <table width="100%" border="0">
        <tr>
            <th width="50%" valign="bottom" align="left">
                Bill To<br>
                {{ $invoice_mst->PartyName }}<br>
                {{ $invoice_mst->Phone }}<br>
                TRN {{ $invoice_mst->TRN }}
            </th>
            <th width="50%">
                <div align="right">
                    <table width="75%" border="0" align="right">
                        <tr>
                            <th align="right" style="text-align:right;">Tax Invoice No :</th>
                            <td align="right" style="text-align:right;">
                                {{ $invoice_mst->InvoiceTypeCode }}{{ $invoice_mst->invoice_no }}</td>
                        </tr>
                        <tr>
                            <th align="right" style="text-align:right;">Invoice Date :</th>
                            <td align="right" style="text-align:right;">{{ $invoice_mst->inv_date }}</td>
                        </tr>
                        <tr>
                            <td align="right" style="text-align:right;">Terms :</td>
                            <td align="right" style="text-align:right;">Due on Receipt</td>
                        </tr>
                        <tr>
                            <td align="right" style="text-align:right;">Due Date :</td>
                            <td align="right" style="text-align:right;">{{ $invoice_mst->due_date }}</td>
                        </tr>
                        <tr>
                            <td align="right" style="text-align:right;">VAT No. :</td>
                            <td align="right" style="text-align:right;">{{ $company->TRN }}</td>
                        </tr>
                    </table>
                </div>
            </th>
        </tr>
    </table>

    {{-- ── LINE ITEMS TABLE — 6 columns: # | Description | Qty | VAT% | VAT Amt | Total ── --}}
    @php
        $total_service = 0;
        $total_tax = 0;
        $total_amount = 0;
    @endphp

    <table width="100%" border="0" cellpadding="4" cellspacing="0">

        {{-- Header --}}
        <tr style="color:white;">
            <td align="center" width="4%" height="25" bgcolor="#333333"><strong>#</strong></td>
            <td align="center" width="52%" bgcolor="#333333"><strong>Item Description</strong></td>
            <td align="center" width="8%" bgcolor="#333333"><strong>Qty</strong></td>
            <td align="center" width="10%" bgcolor="#333333"><strong>VAT %</strong></td>
            <td align="center" width="12%" bgcolor="#333333"><strong>VAT Amt</strong></td>
            <td align="center" width="14%" bgcolor="#333333"><strong>Total</strong></td>
        </tr>

        {{-- Detail rows --}}
        @foreach ($invoice_det as $key => $row)
            @php
                $total_service += $row->service;
                $total_tax += $row->tax_amount;
                $total_amount += $row->total;
            @endphp
            <tr>
                <td align="center" style="padding:6px 4px; vertical-align:top;">{{ ++$key }}</td>

                <td style="padding:6px 4px;">
                    <strong>{{ $row->ItemName }}</strong>

                    @if ($row->pax_name)
                        <br><strong>Leader Name:</strong> {{ $row->pax_name }}
                    @endif

                    @if ($row->contact)
                        <br><strong>Contact:</strong> {{ $row->contact }}
                    @endif

                    @if ($row->pick_point)
                        <br><strong>Pick Point:</strong> {{ $row->pick_point }}
                    @endif

                    @if ($row->departure_date)
                        <br><strong>Departure:</strong> {{ date('d/m/Y', strtotime($row->departure_date)) }}
                    @endif

                    @if ($row->ref_no)
                        <br><strong>Ref #:</strong> {{ $row->ref_no }}
                    @endif

                    @if ($row->SupplierName)
                        <br><strong>Supplier:</strong> {{ $row->SupplierName }}
                    @endif
                </td>

                <td align="center" style="padding:6px 4px; vertical-align:top;">{{ $row->quantity ?? 1 }}</td>
                <td align="center" style="padding:6px 4px; vertical-align:top;">
                    {{ $row->tax_per > 0 ? number_format($row->tax_per, 2) . '%' : '-' }}</td>
                <td align="center" style="padding:6px 4px; vertical-align:top;">
                    {{ number_format($row->tax_amount, 2) }}</td>
                <td align="center" style="padding:6px 4px; vertical-align:top;">{{ number_format($row->total, 2) }}
                </td>
            </tr>
        @endforeach

        {{-- Divider --}}
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>

        {{-- SubTotal row — aligned to correct columns --}}
        <tr>
            <td>&nbsp;</td>
            <td align="right"><strong>SubTotal</strong></td>
            <td align="center"><strong>{{ $invoice_det->sum('quantity') }}</strong></td>
            <td>&nbsp;</td>
            <td align="center"><strong>{{ number_format($total_tax, 2) }}</strong></td>
            <td align="center"><strong>{{ number_format($total_amount, 2) }}</strong></td>
        </tr>

        {{-- Divider --}}
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>

        {{-- Grand totals panel --}}
        <tr>
            <td colspan="6">
                <table width="45%" border="0" align="right" cellpadding="4">
                    <tr>
                        <td align="right" style="padding-right:20px;"><strong>Total</strong></td>
                        <td><strong>AED {{ number_format($invoice_mst->total, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td align="right" style="padding-right:20px;"><strong>Bank Charges</strong></td>
                        <td><strong>AED {{ number_format($invoice_mst->bank_charges, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td align="right" style="padding-right:20px;"><strong>Grand Total</strong></td>
                        <td><strong>AED
                                {{ number_format($invoice_mst->grandtotal ?? $invoice_mst->total, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td align="right" style="padding-right:20px;">Payment Made</td>
                        <td style="color:red;">(-) {{ number_format($invoice_mst->paid, 2) }}</td>
                    </tr>
                    <tr style="background-color:#e9e9e9;">
                        <td align="right" style="padding-right:20px;"><strong>Balance Due</strong></td>
                        <td><strong>AED {{ number_format($balance, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td align="right" style="padding-right:20px;"><strong>Payment Mode</strong></td>
                        <td><strong>{{ $invoice_mst->Payment_mode }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>

    {{-- ── VAT SUMMARY ── --}}
    <p><strong>VAT Summary</strong></p>
    <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr style="background-color:#333333; color:white;">
            <td height="25" align="left" width="50%" style="padding-left:10px;">VAT Detail</td>
            <td align="right">Taxable Amount (AED)</td>
            <td align="right" style="padding-right:10px;">VAT Amount (AED)</td>
        </tr>
        <tr>
            <td height="25" style="padding-left:10px;">Standard Rate (5%)</td>
            <td align="right">AED {{ number_format($total_service, 2) }}</td>
            <td align="right" style="padding-right:10px;">AED {{ number_format($total_tax, 2) }}</td>
        </tr>
    </table>

    <p>Notes</p>
    <p>Thanks for your business.</p>
    <hr>

</body>

</html>
