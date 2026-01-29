<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $pagetitle }}</title>

    @php
        $DrTotal = 0;
        $CrTotal = 0;
        $balance = null;
    @endphp

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            padding: 20px;
            font-size: 12px;
        }

        /* CONTAINER */
        .container {
            width: 100%;
            background: #fff;
            padding: 20px;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            margin-bottom: 25px;
            background: #f2faff;
        }

        .header-table td {
            padding: 15px;
        }

        .company-name {
            color: #0072bc;
            font-size: 18px;
        }

        .title {
            color: #0072bc;
            font-size: 26px;
            text-align: right;
        }

        /* INFO */
        .info-box {
            width: 75%;
            margin: 0 auto 20px auto;
            background: #f2f2f2;
            border: 1px solid #bbb;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .info-row {
            width: 100%;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-col {
            width: 50%;
            float: left;               /* KEY FIX */
            padding: 8px;
            /* box-sizing: border-box; */
            white-space: nowrap;       /* aik hi line */
        }

        .info-col:first-child {
            /* border-right: 1px solid #bbb; */
        }

        /* MAIN TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
                        border: 1px solid #ccc;

        }
       

         #info-table td {
            padding: 6px;
            /* border: 1px solid #ccc; */
            border-color: #ccc;
            border-style: solid;
            border: 0px;
        }

        #reportTable th {
            background: #0072bc;
            color: #fff;
            font-size: 11px;
            padding: 6px;
            border: 1px solid #fff;
        }

        #reportTable td {
            padding: 6px;
            border: 1px solid #ccc;
        }

        .row-alt {
            background: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* SUMMARY */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: 2px solid #ccc;  /* outer table ka border dark */
        }

        .summary-table td {
            border: 1px solid #eee;  /* inner cells light grey */
            padding: 8px;
            font-weight: 700;
        }

        .summary-heading td {
            background: #f2f2f2;     /* light grey background */
            font-size: 16px;
            font-weight: 700;
            text-align: left;       /* heading center */
        }

        .company-logo {
            max-height: 60px;
            max-width: 250px;
        }

        .soa-header {
            width: 100%;
            height: 150px;          /* header bara */
            background: #f3f8fc;
            position: relative;
            margin-bottom: 20px;

        }

        .soa-logo {
            height: 70px;
            position: absolute;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
        }

        .soa-title {
            position: absolute;
            top: 50%;
            left: 450px;              /* RIGHT side le gaya */
            transform: translateY(-50%);
            font-size: 32px;          /* clearly bara */
            font-weight: 700;
            color: #0a4fa3;
            letter-spacing: 1.5px;
        }
    </style>
</head>

<body>

        <div class="soa-header">
            @if(!empty($company->Logo))
                <img 
                    src="{{ public_path('documents/'.$company->Logo) }}" 
                    class="soa-logo"
                    alt="Logo">
            @endif

            <div class="soa-title">
                STATEMENT OF ACCOUNT
            </div>
        </div>

        <!-- PARTY INFO -->
        {{-- <div class="info-box">
            <div class="info-row">
                <div class="info-col">
                    Party Name: {{ $party[0]->PartyName }} - {{ $party[0]->PartyID }}
                </div>
                <div class="info-col">
                    Contact: {{ $party[0]->Phone }}
                </div>
                <div style="clear: both;"></div>
            </div>

            <div class="info-row">
                <div class="info-col">
                    Period: {{ date('d-m-Y', strtotime(session('StartDate'))) }} to {{ date('d-m-Y', strtotime(session('EndDate')))  }}
                </div>
                <div class="info-col">
                    Report Generated On: {{ date('d M, Y') }}
                </div>
                <div style="clear: both;"></div>
            </div>
        </div> --}}
        {{-- <div  style="margin-bottom: 1rem;">
            <table class="table table-bordered w-100" style="font-size:13px;  background-color:#f2f2f2;">
                <tr>
                    <td style="width:10%; border-right:0;">
                        Party Name:
                        {{ $party[0]->PartyName }} - {{ $party[0]->PartyID }}
                    </td>
                    <td style="width:20%; border-left:0;">
                        Contact:
                        {{ $party[0]->Phone }}
                    </td>
                </tr>

                <tr>
                    <td style="width:50%; border-right:0;">
                        Period:
                        {{ date('d M Y', strtotime(session('StartDate'))) }}
                        -
                        {{ date('d M Y', strtotime(session('EndDate'))) }}
                    </td>
                    <td style="width:50%; border-left:0;">
                        Report Generated On:
                        {{ date('d M Y') }}
                    </td>
                </tr>
            </table>
        </div> --}}
        <div  style="text-align: center; width: 80%; margin: 10px auto;">
            <table id="info-table" class="table table-bordered" style="font-size:13px;  background-color:#f2f2f2;">
                <tr>
                    <td style="width: 20%"> Party Name:</td>
                    <td style="width: 30%">{{ $party[0]->PartyName }} - {{ $party[0]->PartyID }}</td>
                    <td style="width: 20%">Contact:</td>
                    <td style="width: 30%"> {{ $party[0]->Phone }}</td>
                </tr>
                <tr>
                    <td> Period:</td>
                    <td>{{ date('d-m-Y', strtotime(session('StartDate'))) }} to {{ date('d-m-Y', strtotime(session('EndDate')))  }}</td>
                    <td>Report Generated On:</td>
                    <td> {{ date('d M, Y') }}</td>
                </tr>

               
            </table>
        </div>



        @if (count($journal) > 0)

            <!-- DATA TABLE -->
            <table id="reportTable" style="width: 100%">

                <thead>
                    <tr>
                        <th style="width10%">Date</th>
                        <th style="width:7%">Voucher NO.</th>
                        <th style="width:3%">Type</th>
                        <th style="width:50%">Description</th>
                        <th style="width:8%">Debit(AED)</th>
                        <th style="width:8%">Credit(AED)</th>
                        <th style="width:14%"> Running Balance</th>
                    </tr>
                </thead>

                <tbody>

                    <!-- OPENING -->
                    <tr>
                        <td class="text-center">—</td>
                        <td class="text-center">—</td>
                        <td class="text-center">—</td>
                        <td>Opening Balance</td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ number_format($sql[0]->Balance, 2) }}</td>
                    </tr>

                    @foreach ($journal as $key => $value)
                        @php
                            if ($balance === null) {
                                $balance = $sql[0]->Balance + ($value->Dr - $value->Cr);
                            } else {
                                $balance += $value->Dr - $value->Cr;
                            }

                            $DrTotal += $value->Dr;
                            $CrTotal += $value->Cr;
                        @endphp

                        <tr class="{{ $key % 2 == 0 ? 'row-alt' : '' }}">
                            <td>{{ date('d-M-Y', strtotime($value->Date)) }}</td>
                            <td class="text-center">{{ $value->VHNO }}</td>
                            <td class="text-center">{{ $value->JournalType }}</td>
                            <td>{{ $value->Narration }}</td>

                            <td class="text-right">
                                {{ $value->Dr == 0 ? '' : number_format($value->Dr, 2) }}
                            </td>

                            <td class="text-right">
                                {{ $value->Cr == 0 ? '' : number_format($value->Cr, 2) }}
                            </td>

                            <td class="text-right">
                                {{ number_format(abs($balance), 2) }} {{ $balance > 0 ? 'DR' : 'CR' }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- TOTAL -->
                    <tr style="font-weight:bold;">
                        <td colspan="3"></td>
                        <td>Totals</td>
                        <td class="text-right">{{ number_format($DrTotal, 2) }}</td>
                        <td class="text-right">{{ number_format($CrTotal, 2) }}</td>
                        <td></td>
                    </tr>

                </tbody>
            </table>

            <!-- SUMMARY -->
            <div style="margin-bottom: 1rem;">
                <table class="summary-table">
                    <tr class="summary-heading">
                        <td colspan="2">Summary</td>
                    </tr>
                    <tr>
                        <td width="30%">Total Debit</td>
                        <td width="70%">{{ number_format($DrTotal, 2) }}</td>
                    </tr>

                    <tr>
                        <td>Total Credit</td>
                        <td>{{ number_format($CrTotal, 2) }}</td>
                    </tr>

                    <tr>
                        <td>Net Balance</td>
                        <td>{{ number_format(abs($balance), 2) }} {{ $balance > 0 ? 'DR' : 'CR' }}</td>
                    </tr>

                </table>
            </div>
        @else
            <p>No Data Found</p>
        @endif

    </div>
    <div>
        <footer>
            Generated by: {{$company->Name}} | {{$company->Email}} | {{$company->Mobile}}
        </footer>
    </div>
</body>

</html>
