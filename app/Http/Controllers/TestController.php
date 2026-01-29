<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TestController extends Controller
{
    public function index1(request $request)
    {
        $allow = check_role(Session::get('UserID'), 'Party Ledger', 'PDF');
        if ($allow[0]->Allow == 'N') {
        return redirect()->back()->with('error', 'You access is limited')->with('class', 'danger');
        }
        ////////////////////////////END SCRIPT ////////////////////////////////////////////////

        Session::put('menu', 'PartyLedger');
        $pagetitle = 'Party Ledger';

        Session::put('StartDate', $request->StartDate);
        Session::put('EndDate', $request->EndDate);

        $vouchertype = DB::table('voucher_type')->where('VoucherTypeID', $request->VoucherTypeID)->get();

        $where = array();

        if ($request->VoucherTypeID > 0) {

        $where = Arr::add($where, 'JournalType', $vouchertype[0]->VoucherCode);
        }

        if ($request->PartyID > 0) {

        $where = Arr::add($where, 'PartyID', $request->PartyID);
        }

        if ($request->ChartOfAccountID > 0) {

        $where = Arr::add($where, 'ChartOfAccountID', $request->ChartOfAccountID);
        }

        $sql = DB::table('journal')
        ->select(DB::raw('sum(if(ISNULL(Dr),0,Dr)-if(ISNULL(Cr),0,Cr)) as Balance'))
        // ->where('PartyID',$request->PartyID)
        ->where($where)
        ->where('Date', '<', $request->StartDate)
        // ->whereBetween('date',array($request->StartDate,$request->EndDate))
        ->get();

        // dd($sql[0]->Balance);
        // $sql= DB::select( DB::raw( 'SET @total := '.$sql[0]->Balance.''));
        // $sql= DB::select( DB::raw( 'select @total as t'));

        $sql[0]->Balance = ($sql[0]->Balance == null) ? '0' :  $sql[0]->Balance;

        // $a = DB::select(DB::raw('select * from v_journal where PartyID = @total'));
        // $journal = DB::select(DB::raw('SELECT a.JournalID, a.ChartOfAccountID, a.*, IF(ISNULL(a.Dr),0,a.Dr) as Dr, a.Cr,sum(if(ISNULL(b.Dr),0,b.Dr)-if(ISNULL(b.Cr),0,b.Cr))+'.$sql[0]->Balance.' as Balance FROM   v_journal a,  v_journal b WHERE b.JournalID <= a.JournalID and a.PartyID='.$request->PartyID.' and b.PartyID='.$request->PartyID.' and a.ChartOfAccountID=110400 and b.ChartOfAccountID=110400 GROUP BY a.JournalID, a.ChartOfAccountID, a.Dr, a.Cr ORDER BY a.JournalID'));
        // $a = DB::table('v_journal')->where('PartyID',DB::raw( '@total'))->get();

        $party = DB::table('party')->where('PartyID', $request->PartyID)->get();

        $journal = DB::table('v_journal')->where('PartyID', $request->PartyID)
        ->whereBetween('Date', array($request->StartDate, $request->EndDate))
        ->where($where)
        ->where('ChartOfAccountID', 110400)
        ->orderBy('Date', 'asc')   // Sort by ID in ascending order
        ->orderBy('JournalID', 'asc')   // Sort by ID in ascending order
        ->get();

        //          $pdf = PDF::loadView ('party_ledger1pdf',compact('journal','pagetitle','sql' ,'party')); 
        // //return $pdf->download('pdfview.pdf');
        //    $pdf->setpaper('A4', 'portiate');
        //       return $pdf->stream();

        // return $pdf->download('pdfview.pdf');
        // return $pdf->stream();
        
        // return view('reports.party_ledger1', compact('journal', 'pagetitle', 'sql', 'party'));
        $company = DB::table('company')->first();
        
        $pdf = PDF::loadView('test.index', compact('journal', 'pagetitle', 'sql', 'party','company'));
        $pdf->setpaper('A4', 'landscape');
        return $pdf->stream();
        // return view('test.index', compact(
        //     'journal',
        //     'pagetitle',
        //     'sql',
        //     'party',
        //     'company',
        // ));
    }

    public function index()
    {
        return view('test.index');
    }

}
