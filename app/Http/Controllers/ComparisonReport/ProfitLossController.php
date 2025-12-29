<?php

namespace App\Http\Controllers\ComparisonReport;

use Carbon\Carbon;
use Illuminate\Http\Request;
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


        return response()->json($dates);

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
