<?php

namespace App\Http\Controllers\ComparisonReport;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProfitLossController extends Controller
{
    public function request()
    {

    }

    public function show(Request $request)
    {
        $fromDate = $request->fromDate ?? date('Y-m-d');
        $toDate = $request->toDate ?? date('Y-m-d');
        $comparedType = $request->comparedType ?? 'period';
        $comparedCount = $request->comparedCount ?? 1;

        // Generate comparison dates
        if ($comparedType === 'year') {
            $dates = $this->comparedYearDates($fromDate, $toDate, $comparedCount);
        } else {
            $dates = $this->comparedMonthDates($fromDate, $toDate, $comparedCount);
        }

        // Level 2 Revenue Accounts
        $revenueAccounts = DB::table('chartofaccount')
            ->where('CODE', 'R')
            ->where('Level', 2)
            ->get();

        $revenue = [];

        foreach ($revenueAccounts as $level2) {

            // Reset Level 2 container
            $level2Data = [
                'level2Name' => $level2->ChartOfAccountName,
                'level3' => []
            ];

            // Level 3 Accounts
            $level3Accounts = DB::table('chartofaccount')
                ->where('CODE', 'R')
                ->where('Level', 3)
                ->where('L2', $level2->ChartOfAccountID)
                ->get();

            foreach ($level3Accounts as $level3) {

                $level3Data = [];

                foreach ($dates as $date) {

                    $journal = DB::table('journal')
                        ->where('ChartOfAccountID', $level3->ChartOfAccountID)
                        ->whereBetween('Date', [$date['fromDate'], $date['toDate']])
                        ->selectRaw('SUM(Dr) as dr, SUM(Cr) as cr')
                        ->first();

                    $level3Data[] = [
                        'label' => $date['label'],
                        'dr' => $journal->dr ?? 0,
                        'cr' => $journal->cr ?? 0,
                    ];
                }

                //check balance if whole date range is zero then dont add that
                
                $totalBalance = collect($level3Data)->sum(function ($item) {
                    return $item['cr'] - $item['dr'];
                });

                if($totalBalance != 0)
                {
                    $level2Data['level3'][] = [
                        'name' => $level3->ChartOfAccountName,
                        'data' => $level3Data
                    ];
                }
                
                /*
                $level2Data['level3'][] = [
                    'name' => $level3->ChartOfAccountName,
                    'data' => $level3Data
                ];
                */

            }
            $revenue[] = $level2Data;
        }

        // return response()->json($revenue);
        return view('comparison_reports.profit_loss.show', compact(
            'fromDate',
            'toDate',
            'comparedType',
            'comparedCount',
            'revenue',
            'dates'
        ));

    }


    public function comparedMonthDates($fromDate, $toDate, $comparedCount)
    {
        $dates = [];

        for ($i = 0; $i < $comparedCount; $i++) {

            $from = Carbon::parse($fromDate)->subMonthsNoOverflow($i);
            $to   = Carbon::parse($toDate)->subMonthsNoOverflow($i);

            $dates[] = [
                'label'    => $from->format('F Y'), // Month Name + Year
                'fromDate' => $from->format('Y-m-d'),
                'toDate'   => $to->format('Y-m-d'),
            ];
        }

        return $dates;
    }

    public function comparedYearDates($fromDate, $toDate, $comparedCount)
    {
        $dates = [];

        for ($i = 0; $i < $comparedCount; $i++) {

            $from = Carbon::parse($fromDate)->subYearsNoOverflow($i);
            $to   = Carbon::parse($toDate)->subYearsNoOverflow($i);

            $dates[] = [
                'label'    => $from->format('Y'), // Year label
                'fromDate' => $from->format('Y-m-d'),
                'toDate'   => $to->format('Y-m-d'),
            ];
        }

        return $dates;
    }
}
