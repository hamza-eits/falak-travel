@extends('tmp')

@push('styles')
<style>
    body {
        font-family: Arial, sans-serif;
        background: #fff;
        color: #333;
        font-size: 13px;
    }

    .document-wrapper {
        max-width: 100%;
        width: 100%;
        margin: 0;
        box-sizing: border-box;
        padding: 20px;
    }

    /* Header */
    .top-header {
        background: #f2f8ff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 25px 40px;
        margin-bottom: 25px;
    }

    .header-title {
        font-size: 26px;
        color: #0072bc;
        letter-spacing: 1px;
    }

    /* Party Details */
    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border: 1px solid #cfcfcf;
        margin-bottom: 25px;
        font-size: 13px;
    }

    .details-item {
        padding: 8px 12px;
        border-bottom: 1px solid #ededed;
    }

    .details-item.left { text-align: left; }
    .details-item.right { text-align: right; }

    .details-item.full {
        grid-column: 1 / -1;
        text-align: center;
        font-weight: bold;
        font-size: 15px;
    }

    .details-item.red { color: red; }
    .details-item.label { font-weight: 600; }
    .details-item.no-border { border-bottom: none; }

    /* Table */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #0072bc;
        color: #fff;
        padding: 6px 8px;
        font-size: 11px;
        text-align: center;
        border: 1px solid #fff;
        white-space: nowrap;
    }

    td {
        padding: 8px;
        border: 1px solid #dcdcdc;
        vertical-align: top;
    }

    td:nth-child(5) {
        word-break: break-word;
        line-height: 1.4;
    }

    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .bg-gray { background: #cccccc; }

    /* Footer */
    .footer {
        margin-top: 25px;
        font-size: 13px;
        color: #444;
    }

    @media print {
        .document-wrapper {
            max-width: 1000px;
            margin: auto;
        }
    }
</style>
@endpush

@section('content')

<div class="document-wrapper">

    {{-- HEADER (Optional) --}}
    {{--
    <div class="top-header">
        <img src="https://via.placeholder.com/150x50?text=FALAK+LOGO" height="50">
        <div class="header-title">STATEMENT OF ACCOUNT</div>
    </div>
    --}}

    <!-- PARTY DETAILS -->
    <div class="details-grid">

        <!-- Company -->
        <div class="details-item full">
            {{ $company[0]->Name }}
        </div>

        <!-- Address | TRN -->
        <div class="details-item left">
            {{ $company[0]->Address }}
        </div>
        <div class="details-item right">
            TRN : {{ $company[0]->TRN }}
        </div>

        <!-- Party | Ledger -->
        <div class="details-item left red label">
            {{ $party[0]->PartyID }} - {{ $party[0]->PartyName }}
        </div>
        <div class="details-item right red">
            Ledger Account
        </div>

        <!-- Contact | Period -->
        <div class="details-item left red">
            Contact : {{ $party[0]->Phone }}
        </div>
        <div class="details-item right red">
            From {{ dateformatman2(request()->StartDate) }}
            To {{ dateformatman2(request()->EndDate) }}
        </div>

        <!-- Date -->
        <div class="details-item left label no-border">
            Dated: {{ date('d-m-Y') }}
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
                    <th style="width:8%">VHNO</th>
                    <th style="width:10%">DATE</th>
                    <th style="width:8%">Salesman</th>
                    <th style="width:15%">C.O</th>
                    <th style="width:30%">Description</th>
                    <th style="width:8%">DR</th>
                    <th style="width:8%">CR</th>
                    <th style="width:14%">Balance</th>
                </tr>
            </thead>

            <tbody>
            @if(!$journal->isEmpty())
                @foreach($journal as $value)

                    @php
                        $url = '';
                        if (str_starts_with($value->VHNO, 'UI')) {
                            $url = URL('/UmrahEdit/' . $value->InvoiceMasterID);
                        } elseif (str_starts_with($value->VHNO, 'SI')) {
                            $url = URL('/InvoiceEdit/' . $value->InvoiceMasterID);
                        }

                        $balance = is_null($balance)
                            ? ($value->Dr - $value->Cr)
                            : $balance + ($value->Dr - $value->Cr);

                        $DrTotal += $value->Dr;
                        $CrTotal += $value->Cr;
                    @endphp

                    <tr>
                        <td class="text-center">
                            <a href="{{ $url }}" target="_blank">{{ $value->VHNO }}</a>
                        </td>

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

                        <td>
                            {{ $value->Narration }}

                            @if (substr($value->VHNO,0,3) == 'TAX' || substr($value->VHNO,0,3) == 'INV')
                                <div style="color:red">
                                    @foreach(DB::table('v_invoice_detail')
                                        ->where('InvoiceMasterID',$value->InvoiceMasterID)
                                        ->get() as $item)
                                        {{ $item->ItemName }}
                                        {{ $item->Qty }} x
                                        {{ $item->Rate }} =
                                        {{ $item->Total }}
                                        <br>
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        <td class="text-right">
                            {{ $value->Dr ? number_format($value->Dr,2) : '' }}
                        </td>

                        <td class="text-right">
                            {{ $value->Cr ? number_format($value->Cr,2) : '' }}
                        </td>

                        <td class="text-right">
                            {{ number_format(abs($balance),2) }}
                            {{ $balance >= 0 ? 'DR' : 'CR' }}
                        </td>
                    </tr>

                @endforeach

                <!-- TOTAL -->
                <tr class="bg-gray">
                    <td colspan="5" class="text-center"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>{{ number_format($DrTotal,2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($CrTotal,2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($DrTotal - $CrTotal,2) }}</strong></td>
                </tr>
            @else
                <tr>
                    <td colspan="8" class="text-center bg-gray">
                        <strong>No Data Found</strong>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Generated by <b>XTBOOKS – Extensive IT Services</b> |
        www.xtbooks.com | +971 50 173 4344
    </div>

</div>

@endsection