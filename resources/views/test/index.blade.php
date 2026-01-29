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
            font-size: 13px;
            font-weight: 700;
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
        }

        th {
            background: #0072bc;
            color: #fff;
            font-size: 11px;
            padding: 6px;
            border: 1px solid #fff;
        }

        td {
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
        <div class="info-box">
    <div class="info-row">
        <div class="info-col">
            <b>Party Name:</b> {{ $party[0]->PartyName }} - {{ $party[0]->PartyID }}
        </div>
        <div class="info-col">
            <b>Contact:</b> {{ $party[0]->Phone }}
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="info-row">
        <div class="info-col">
            <b>Period:</b> {{ session('StartDate') }} TO {{ session('EndDate') }}
        </div>
        <div class="info-col">
            <b>Report Date:</b> {{ date('d-M-Y') }}
        </div>
        <div style="clear: both;"></div>
    </div>
</div>


        @if (count($journal) > 0)

            <!-- DATA TABLE -->
            <table>

                <thead>
                    <tr>
                        <th style="width:5%">Date</th>
                        <th style="width:7%">VHNO</th>
                        <th style="width:5%">Type</th>
                        <th style="width:49%">Description</th>
                        <th style="width:5%">DR</th>
                        <th style="width:5%">CR</th>
                        <th style="width:10%">Balance</th>
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
                            <td>{{ dateformatman($value->Date) }}</td>
                            <td>{{ $value->VHNO }}</td>
                            <td>{{ $value->JournalType }}</td>
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
        @else
            <p>No Data Found</p>
        @endif

    </div>
</body>

</html>
