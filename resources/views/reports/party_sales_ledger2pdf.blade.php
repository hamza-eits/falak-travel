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

        tbody{
            display:table-row-group;
        }

        tr{
            page-break-inside:avoid;
            page-break-after:auto;
        }

    }

</style>
@endpush


@section('content')

 <div class="main-content">
                
                <div class="page-content">
                    <div class="container-fluid">


    <!-- COMPANY -->

    <div class="details-grid">

        <div class="details-item full">
            {{ $company[0]->Name }}
        </div>

        <div class="details-item left">
            {{ $company[0]->Address }}
        </div>

        <div class="details-item right">
            TRN : {{ $company[0]->TRN }}
        </div>

        <div class="details-item left red label">
            {{ $party[0]->PartyID }} - {{ $party[0]->PartyName }}
        </div>
        <div class="details-item right red"></div>

        <div class="details-item left red">
            Contact : {{ $party[0]->Phone }}
        </div>
        <div class="details-item right red"></div>

        <div class="details-item left label no-border">
            Dated : {{ date('d-m-Y') }}
        </div>
        <div class="details-item right no-border"></div>

    </div>

    @php
        $DrTotal = 0;
        $CrTotal = 0;
        $balance = null;
    @endphp

    <!-- TABLE -->

    <div class="table-responsive">
        <table>

            <thead>
                <tr>
                    <th style="width:5%">VHNO</th>
                    <th style="width:7%">DATE</th>
                    <th style="width:8%">Salesman</th>
                    <th style="width:10%">C.O</th>
                    <th style="width:45%">Description</th>
                    <th style="width:7%">DR</th>
                    <th style="width:7%">CR</th>
                    <th style="width:11%">Balance</th>
                </tr>
            </thead>

            <tbody>

            @foreach($journal as $value)

                @php
                    $balance = is_null($balance)
                        ? ($value->Dr - $value->Cr)
                        : $balance + ($value->Dr - $value->Cr);

                    $DrTotal += $value->Dr;
                    $CrTotal += $value->Cr;
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
                    <td>{{ $value->Narration }}</td>
                    <td class="text-right">{{ $value->Dr ? number_format($value->Dr,2) : '' }}</td>
                    <td class="text-right">{{ $value->Cr ? number_format($value->Cr,2) : '' }}</td>
                    <td class="text-right">
                        {{ number_format(abs($balance),2) }} {{ $balance >= 0 ? 'DR' : 'CR' }}
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
