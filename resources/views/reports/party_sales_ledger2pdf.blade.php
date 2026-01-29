@extends('tmp')

@push('styles')
<style>

    /* ================= BASE ================= */

    body{
        font-family: Arial, sans-serif;
        background:#fff;
        color:#333;
        font-size:13px;
    }

    .document-wrapper{
        width:100%;
        padding:20px;
        box-sizing:border-box;
    }

    /* ================= HEADER ================= */

    .details-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        border:1px solid #cfcfcf;
        margin-bottom:20px;
        font-size:13px;
    }

    .details-item{
        padding:8px 12px;
        border-bottom:1px solid #ededed;
    }

    .details-item.left{ text-align:left; }
    .details-item.right{ text-align:right; }
    .details-item.full{
        grid-column:1 / -1;
        text-align:center;
        font-weight:bold;
        font-size:15px;
    }
    .details-item.red{ color:red; }
    .details-item.label{ font-weight:600; }
    .details-item.no-border{ border-bottom:none; }

    /* ================= TABLE ================= */

    .table-responsive{
        width:100%;
    }

    table{
        width:100%;
        border-collapse:collapse;
        table-layout:fixed;
    }

    th{
        background:#0072bc;
        color:#fff;
        padding:6px;
        font-size:11px;
        text-align:center;
        border:1px solid #fff;
        white-space:nowrap;
    }

    td{
        padding:5px 6px;
        border:1px solid #dcdcdc;
        font-size:12px;
        line-height:1.35;
        vertical-align:middle;
        word-wrap:break-word;
        overflow-wrap:break-word;
    }

    tbody tr:nth-child(even){
        background:#f2f2f2;
    }

    td:nth-child(5){
        white-space:normal;
    }

    .text-right{ text-align:right; }
    .text-center{ text-align:center; }

    .total-row{
        background:#d9d9d9 !important;
        font-weight:bold;
    }

    /* ================= SUMMARY ================= */

    .summary-wrapper{
        border:1px solid #cfcfcf;
        margin-top:20px;
        max-width:450px;
    }

    .summary-title{
        background:#f2f2f2;
        padding:6px 12px;
        border-bottom:1px solid #cfcfcf;
        font-size:14px;
        font-weight:bold;
    }

    .summary-row{
        display:flex;
        justify-content:space-between;
        padding:6px 12px;
        font-size:13px;
        border-bottom:1px solid #ededed;
    }

    .summary-row:last-child{
        border-bottom:none;
    }

    .summary-label{
        font-weight:600;
    }

    /* ================= FOOTER ================= */

    .footer{
        margin-top:25px;
        font-size:13px;
        color:#444;
    }

    /* ================= PRINT FIXES ================= */

    @media print{

        @page{
            size:A4;
            margin:12mm;
        }

        .document-wrapper{
            width:100%;
            margin:0;
        }

        .table-responsive{
            overflow:visible !important;
        }

        table{
            page-break-inside:auto;
        }

        thead{
            display:table-header-group;
        }

        body,
        td,
        th {
            font-size: 13px;
        }

        -->
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="noborder_table">
        <tr>
            <td colspan="2">
                <div align="center" class="style1">{{ $company[0]->Name }}</div>

                <div align="center">{{ $company[0]->Address }}</div>
                <div align="center">TRN : {{ $company[0]->TRN }}</div>
            </td>
        </tr>
        <tr style="color: red;">
            <td colspan="2">
                <div align="center" class="style1" style="color: red;">
                    {{ $party[0]->PartyID }}-{{ $party[0]->PartyName }} </div>
                <div align="center">Ledger Account</div>
            </td>
        </tr>
        <tr style="color: red;">
            <td colspan="2">
                <div align="center">Contact : {{ $party[0]->Phone }}</div>
            </td>
        </tr>
        <tr style="color: red;">
            <td colspan="2">
                <div align="center">From {{ dateformatman2(request()->StartDate) }} To
                    {{ dateformatman2(request()->EndDate) }}
                </div>
            </td>
        </tr>
        <tr>
            <td width="50%">Dated: {{ date('d-m-Y') }}</td>
            <td width="50%">&nbsp;</td>
        </tr>
    </table>

    <?php
    $DrTotal = 0;
    $CrTotal = 0;
    
    ?>

    <script type="text/php">
    if ( isset($pdf) ) {
        $font = Font_Metrics::get_font("helvetica", "bold");
        $pdf->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
    }
</script>

    <table width="60%" border="1" style="font-size: 10pt;" align="center">

        <tr>
            <th bgcolor="#CCCCCC"><strong>VHNO</strong></th>
            <th bgcolor="#CCCCCC"><strong>DATE</strong></th>
            <th bgcolor="#CCCCCC"><strong>Saleman</strong></th>
            <th bgcolor="#CCCCCC"><strong>C.O</strong></th>
            <th bgcolor="#CCCCCC"><strong>Description</strong></th>
            <th bgcolor="#CCCCCC"><strong>DR</strong></th>
            <th bgcolor="#CCCCCC"><strong>CR</strong></th>
            <th bgcolor="#CCCCCC"><strong>Balance</strong></th>
        </tr>



        @if (!$journal->isEmpty())
            @foreach ($journal as $key => $value)
                <?php
                $invoice_master = DB::table('invoice_master')->where('InvoiceMasterID', $value->InvoiceMasterID)->get();
                ?>
                <tr valign="top">
                    <td style="border: 1px solid black;" valign="top">

                        @php
                            $url = '';
                            if (str_starts_with($value->VHNO, 'UI')) {
                                $url = URL('/UmrahEdit/' . $value->InvoiceMasterID);
                            } elseif (str_starts_with($value->VHNO, 'SI')) {
                                $url = URL('/InvoiceEdit/' . $value->InvoiceMasterID);
                            }
                        @endphp

                <tr>
                    <td class="text-center">{{ $value->VHNO }}</td>
                    <td class="text-center">{{ dateformatman($value->Date) }}</td>
                    <td class="text-center">
                        {{ DB::table('v_invoice_master')
                            ->where('InvoiceMasterID',$value->InvoiceMasterID)
                            ->pluck('FullName')
                            ->first() }}
                    </td>
                    <td class="text-center">
                        <a href="{{ URL('/VoucherEdit/'.$value->VoucherMstID) }}" target="_blank">
                            Edit VH#
                        </a>
                    </td>
                    <td style="border: 1px solid black;" valign="top">{{ dateformatman($value->Date) }}</td>
                    <td style="border: 1px solid black;" valign="top">{{ DB::table('v_invoice_master')->where('InvoiceMasterID',$value->InvoiceMasterID)->pluck('FullName')->first() }}</td>
                    <td style="border: 1px solid black;" valign="top"><a
                            href="{{ URL('/VoucherEdit' . '/' . $value->VoucherMstID) }}" title=""
                            target="_blank">Edit VH#</a></td>
                    <td align="left" style="border: 1px solid black;  " valign="top">{{ $value->Narration }}<div
                            style="color: red;">
                            @if (substr($value->VHNO, 0, 3) == 'TAX' || substr($value->VHNO, 0, 3) == 'INV')
                                <?php
                                
                                $invoice_detail = DB::table('v_invoice_detail')->where('InvoiceMasterID', $value->InvoiceMasterID)->get();
                                
                                ?>

                                @if (!$invoice_detail->isEmpty())
                                    @foreach ($invoice_detail as $key => $value1)
                                        {{ $value1->ItemName }} {{ $value1->Qty }} Qty x {{ $value1->Rate }} Rate
                                        ={{ $value1->Total }} <br>
                                    @endforeach
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>

            @endforeach

            <tr class="total-row">
                <td colspan="5" class="text-center">TOTAL</td>
                <td class="text-right">{{ number_format($DrTotal,2) }}</td>
                <td class="text-right">{{ number_format($CrTotal,2) }}</td>
                <td class="text-right">{{ number_format($DrTotal - $CrTotal,2) }}</td>
            </tr>

            </tbody>

        </table>
    </div>

    <!-- SUMMARY -->

    <div class="summary-wrapper">
        <div class="summary-title">Summary</div>

        <div class="summary-row">
            <div class="summary-label">Total Debit</div>
            <div>AED {{ number_format($DrTotal,2) }}</div>
        </div>

        <div class="summary-row">
            <div class="summary-label">Total Credit</div>
            <div>AED {{ number_format($CrTotal,2) }}</div>
        </div>

        <div class="summary-row">
            <div class="summary-label">Net Balance</div>
            <div>AED {{ number_format($DrTotal - $CrTotal,2) }}</div>
        </div>
    </div>

    <div class="footer">
        Generated by <b>XTBOOKS – Extensive IT Services</b>
    </div>

</div>
</div>
</div>

@endsection
