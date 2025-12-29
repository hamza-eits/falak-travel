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

        $dates = [];
        
        if($comparedType == 'period')
        {
            $dates = $this->comparedMonthDates($fromDate,$toDate,$comparedCount);
        }

        if($comparedType == 'year')
        {
            $dates = $this->comparedYearDates($fromDate,$toDate,$comparedCount);
        }

        //Level 2 Accounts
        $revenueAccounts = DB::table('chartofaccount')
        ->where('CODE','R')
        ->where('Level',2)
        ->get();

        $revenue = [];
        
        
        foreach($dates as $date)
        {

            //Accessing Each Level 2 , child Level 3 
            foreach($revenueAccounts as $level2)
            {
                $level3Accounts = DB::table('chartofaccount')
                ->where('CODE','R')
                ->where('Level',3)
                ->where('L2', $level2->ChartOfAccountID)
                ->get(); 

                $level2Data = [];
                $level3Data = [];
                foreach($level3Accounts as $level3)
                {
                    $level3Data[] = [
                        'name' => $level3->ChartOfAccountName,
                        'dr' => DB::table('journal')
                            ->where('ChartOfAccountID',$level3->ChartOfAccountID)
                            ->whereBetween('Date',[$date['fromDate'], $date['toDate']])
                            ->sum('Dr'),
                        'cr' => DB::table('journal')
                            ->where('ChartOfAccountID',$level3->ChartOfAccountID)
                            ->whereBetween('Date',[$date['fromDate'], $date['toDate']])
                            ->sum('Cr'),
                    ];
                }
                $level2Data = [
                    'level2Name' => $level2->ChartOfAccountName,
                    'level3Data' => $level3Data,
                ];


                $revenue [] = [
                    'date' => $date['fromDate'],
                    'data' => $level2Data
                ];

            }


            return response()->json($revenue);
        }






































        //Accessing Each Level 2 , child Level 3 
        foreach($revenueAccounts as $level2)
        {
            $level3Data = [];
            
            $level3Accounts = DB::table('chartofaccount')
            ->where('CODE','R')
            ->where('Level',3)
            ->where('L2', $level2->ChartOfAccountID)
            ->get();

            foreach($level3Accounts as $level3)
            {
                $data = [];
                foreach($dates as $date)
                {
                    $data[] = [
                        'name' => $level3->ChartOfAccountName,
                        'dr' => DB::table('journal')
                            ->where('ChartOfAccountID',$level3->ChartOfAccountID)
                            ->whereBetween('Date',[$date['fromDate'], $date['toDate']])
                            ->sum('Dr'),
                        'cr' => DB::table('journal')
                            ->where('ChartOfAccountID',$level3->ChartOfAccountID)
                            ->whereBetween('Date',[$date['fromDate'], $date['toDate']])
                            ->sum('Cr'),
                    ];
                }
            }

            $revenue['level3Data'] = $data;
        }

        $revenue[] = [
                'level2Name' => $level2->ChartOfAccountName,
                'level3Data' => ''
            ];


        return response()->json($revenue);

        return view('comparison_reports.profit_loss.show');
    }





















    public function comparedMonthDates($fromDate,$toDate,$comparedCount)
    {
        $dates = [];
        for($i = 0; $i < $comparedCount; $i++)
        {
            $dates[] = [
                'fromDate' => Carbon::parse($fromDate)->subMonthsNoOverflow($i)->format('Y-m-d'),
                'toDate' => Carbon::parse($toDate)->subMonthsNoOverflow($i)->format('Y-m-d'),
            ];
        }

        return $dates;
    }
    public function comparedYearDates($fromDate,$toDate,$comparedCount)
    {
        $dates = [];
        for($i = 0; $i < $comparedCount; $i++)
        {
            $dates[] = [
                'fromDate' => Carbon::parse($fromDate)->subYearsNoOverflow($i)->format('Y-m-d'),
                'toDate' => Carbon::parse($toDate)->subYearsNoOverflow($i)->format('Y-m-d'),
            ];
        }

        return $dates;
    }
}
