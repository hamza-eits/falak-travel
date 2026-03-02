<?php

namespace App\Http\Controllers;

use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class TourBookingController extends Controller
{
    // ============================================================
    // INDEX
    // ============================================================
    public function index(Request $request)
    {
        Session::put('menu', 'Invoice');
        $pagetitle = 'Tour Booking';

        $allow = check_role(Session::get('UserID'), 'Invoice', 'List');
        if ($allow[0]->Allow == 'N') {
            return redirect()->back()->with('error', 'Your access is limited')->with('class', 'danger');
        }

        Session()->forget('LeadID');
        Session()->forget('PartyID');

        if ($request->ajax()) {
            try {

                $query = DB::table('tour_booking_details as tb')
                    ->leftJoin('tour_booking_master as tbm', 'tbm.id', '=', 'tb.tourmaster_id')
                    ->leftJoin('item as i',   'i.ItemID',   '=', 'tb.item_id')
                    ->leftJoin('party as p',  'p.PartyID',  '=', 'tbm.party_id')
                    ->select(
                        'tb.id',
                        'tb.pax_name',
                        'tb.contact',
                        'tb.quantity',
                        'tb.departure_date',
                        'tb.pick_point',
                        'tb.fare',
                        'tb.ref_no',
                        'tb.tax_per',
                        'tb.tax_amount',
                        'tb.service',
                        'tb.total',
                        'tbm.paid',
                        'tbm.id as tourmaster_id',
                        'tbm.invoice_no',
                        'tbm.inv_date',
                        'tbm.reference_no',
                        'tbm.Payment_mode',
                        'tbm.voucher',
                        'tbm.grandtotal',
                        'tbm.balance',
                        'tbm.party_id',
                        'tbm.salesman_id',
                        'tbm.invoicetype_id',

                        'p.PartyName',
                        'p.Phone',
                        'i.ItemName'
                    );

                // Non-Admin sees only their own
                if (Session::get('UserType') !== 'Admin') {
                    $query->where('tbm.salesman_id', Session::get('UserID'));
                }

                if ($request->filled('party_name')) {
                    $query->where('p.PartyName', 'like', '%' . $request->party_name . '%');
                }

                if ($request->filled('Phone')) {
                    $last9 = substr(str_replace(' ', '', trim($request->Phone)), -9);
                    $query->where(DB::raw("REPLACE(p.Phone,' ','')"), 'like', '%' . $last9 . '%');
                }

                if ($request->filled('startdate')) {
                    $query->whereDate('tbm.inv_date', '>=', $request->startdate);
                }

                if ($request->filled('enddate')) {
                    $query->whereDate('tbm.inv_date', '<=', $request->enddate);
                }

                if ($request->filled('UserID')) {
                    $query->where('tbm.salesman_id', $request->UserID);
                }

                if ($request->filled('ItemID')) {
                    $query->where('tb.item_id', $request->ItemID);
                }

                $data = $query->orderBy('tb.id', 'desc')->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        return '
                        <div class="d-flex align-items-center col-actions">
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle card-drop" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-dots-horizontal font-size-18"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>
                                        <a href="javascript:void(0)"
                                           onclick="openLedgerModal(' . $row->party_id . ', \'' . addslashes($row->PartyName) . '\')"
                                           class="dropdown-item">
                                           <i class="mdi mdi-account-details me-1" style="color:#FF5733;"></i>
                                           <strong>Party Ledger</strong>
                                        </a>
                                    </li>

                                    <div class="dropdown-divider"></div>

                                    <li>
                                        <a href="#" class="dropdown-item record-payment"
                                        data-invoicemasterid="' . $row->tourmaster_id . '"
                                        data-invoicetypeid="'   . $row->invoicetype_id . '">
                                            <i class="mdi mdi-cash-usd font-size-18 text-success me-1"></i>
                                            Record Payment
                                        </a>
                                    </li>

                                    <li>
                                        <a href="javascript:void(0)"
                                           onclick="loadPDF(\'' . URL('/tour-booking/pdf-view/' . $row->tourmaster_id) . '\')"
                                           class="dropdown-item">
                                           <i class="mdi mdi-file-pdf me-1" style="color:#FF5733;"></i>
                                           View Invoice
                                        </a>
                                    </li>

                                    <li>
                                        <a href="' . URL('/tour-booking/edit/' . $row->tourmaster_id) . '" class="dropdown-item">
                                            <i class="bx bx-pencil text-secondary me-1"></i>
                                            Edit Tour Booking
                                        </a>
                                    </li>

                                    <div class="dropdown-divider"></div>

                                    <li>
                                        <a href="javascript:void(0)"
                                           onclick="delete_booking(' . $row->tourmaster_id . ')"
                                           class="dropdown-item">
                                           <i class="bx bx-trash text-danger me-1"></i>
                                           Delete Tour Booking
                                        </a>
                                    </li>
                                    <li>
                                        <a href="' . URL('/tour-booking/pdf/' . $row->tourmaster_id) . '/download' . '" target="_blank" class="dropdown-item">
                                            <i class="mdi mdi-file-pdf-outline font-size-16 me-1" style="color:#AF0505;"></i> Download Invoice
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);

            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        $chartofaccount = DB::table('chartofaccount')->where('ChartOfAccountID', '560110')->get();

        return view('tour_booking.index', compact('pagetitle', 'chartofaccount'));
    }

    // ============================================================
    // CREATE
    // ============================================================
    public function create()
    {
        Session::put('menu', 'Invoice');

        $invoice_type = DB::table('invoice_type')->whereIn('InvoiceTypeID', [1, 2])->get();
        $items        = DB::table('item')->where('ItemType', 'I')->get();
        $supplier     = DB::table('supplier')->get();
        $party        = DB::table('party')->get();

        $query = DB::table('user')->where('Active', 'Yes');
        if (Session::get('UserType') !== 'Admin') {
            $query->where('UserID', Session::get('UserID'));
        }
        $saleman = $query->get();

        $vhno = DB::table('tour_booking_master')
            ->select(DB::raw('IFNULL(MAX(id) + 1, 1) as VHNO'))
            ->get();

        $invoicetypes = DB::table('invoice_type')->get();

        return view('tour_booking.create', compact(
            'invoice_type', 'items', 'supplier', 'party', 'saleman', 'vhno', 'invoicetypes'
        ));
    }

    // ============================================================
    // SAVE
    // ============================================================
    public function save(Request $request)
    {
        $allow = check_role(Session::get('UserID'), 'Invoice', 'Create');
        if ($allow[0]->Allow == 'N') {
            return redirect()->back()->with('error', 'Your access is limited')->with('class', 'danger');
        }

        try {
            DB::beginTransaction();

            $invoice_type = DB::table('invoice_type')->where('InvoiceTypeID', $request->input('InvoiceTypeID'))->first();
            $type_code    = $invoice_type->InvoiceTypeCode;
            $vhno         = $request->input('VHNO');
            $party_id     = $request->input('party_id');
            $date         = $request->input('inv_date');

            // 1. Master
            $tourmaster_id = DB::table('tour_booking_master')->insertGetId([
                'invoicetype_id' => $request->input('InvoiceTypeID'),
                'party_id'       => $party_id,
                'invoice_no'     => $vhno,
                'inv_date'       => $date,
                'due_date'       => $request->input('due_date'),
                'reference_no'   => $request->input('reference_no'),
                'salesman_id'    => $request->input('salesman_id'),
                'source'         => $request->input('source'),
                'note'           => $request->input('note'),
                'Payment_mode'   => $request->input('bank_name')    ?? '',
                'bank_name'      => $request->input('bank_name')    ?? '',
                'vat_per'        => $request->input('vat_per')      ?? 0,
                'bank_charges'   => $request->input('bank_charges') ?? 0,
                'total'          => $request->input('total')        ?? 0,
                'grandtotal'     => $request->input('grandtotal')   ?? 0,
                'paid'           => $request->input('paid')         ?? 0,
                'balance'        => $request->input('balance')      ?? 0,
                'lead_id'        => Session::get('LeadID')          ?? 0,
                'voucher'        => '',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // 2. Log
            DB::table('log')->insert([
                'UserName'  => Session::get('FullName'),
                'Amount'    => $request->input('total'),
                'Date'      => date('Y-m-d H:i:s'),
                'Section'   => 'Tour Booking Create',
                'VHNO'      => $tourmaster_id,
                'Narration' => 'Tour Booking Created',
                'Trace'     => 101,
                'UserID'    => Session::get('UserID'),
            ]);

            // 3. Line items
            $item_ids = $request->input('item_id', []);

            for ($i = 0; $i < count($item_ids); $i++) {

                if (empty($item_ids[$i])) continue;

                $qty        = intval($request->quantity[$i]    ?? 1);
                $fare       = floatval($request->fare[$i]       ?? 0);
                $total_fare = floatval($request->total_fare[$i] ?? ($fare * $qty));
                $tax_amt    = floatval($request->tax_amount[$i] ?? 0);
                $service    = floatval($request->service[$i]    ?? 0);
                $row_total  = floatval($request->row_total[$i]  ?? 0);
                $sup_id     = $request->supplier_id[$i]         ?? null;
                $pax_name   = $request->pax_name[$i]            ?? '';

                DB::table('tour_booking_details')->insert([
                    'tourmaster_id'  => $tourmaster_id,
                    'item_id'        => $item_ids[$i],
                    'supplier_id'    => $sup_id,
                    'quantity'       => $qty,
                    'pax_name'       => $pax_name,
                    'contact'        => $request->contact[$i]        ?? null,
                    'departure_date' => !empty($request->departure_date[$i]) ? $request->departure_date[$i] : null,
                    'pick_point'     => $request->pick_point[$i]     ?? null,
                    'ref_no'         => $request->ref_no[$i]         ?? null,
                    'fare'           => $fare,
                    'total_fare'     => $total_fare,
                    'tax_per'        => $request->tax_per[$i]        ?? 0,
                    'tax_amount'     => $tax_amt,
                    'service'        => $service,
                    'total'          => $row_total,
                    'paid'           => 0,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                $j = [
                    'VHNO'            => $type_code . $vhno,
                    'JournalType'     => $type_code,
                    'SupplierID'      => $sup_id,
                    'PartyID'         => $party_id,
                    'InvoiceMasterID' => $tourmaster_id,
                    'Date'            => $date,
                    'Narration'       => $pax_name,
                ];

                if ($request->input('InvoiceTypeID') == 1) {
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '110400', 'Dr' => $row_total,  'Trace' => 105]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '510103', 'Cr' => $total_fare, 'Trace' => 106]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '510103', 'Dr' => $total_fare, 'Trace' => 109]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '210100', 'Cr' => $total_fare, 'Trace' => 110]));
                    $service >= 0
                        ? DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '410101', 'Cr' => $service,      'Trace' => 107]))
                        : DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '410101', 'Dr' => abs($service), 'Trace' => 108]));
                    $tax_amt >= 0
                        ? DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => 210300, 'Cr' => $tax_amt,      'Trace' => 111]))
                        : DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => 210300, 'Dr' => abs($tax_amt), 'Trace' => 111]));
                }

                if ($request->input('InvoiceTypeID') == 2) {
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '110400', 'Cr' => $row_total,  'Trace' => 201]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '510103', 'Dr' => $total_fare, 'Trace' => 204]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '510103', 'Cr' => $total_fare, 'Trace' => 205]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '210100', 'Dr' => $total_fare, 'Trace' => 206]));
                    $service >= 0
                        ? DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '410101', 'Dr' => $service,      'Trace' => 202]))
                        : DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '410101', 'Cr' => abs($service), 'Trace' => 2022]));
                    $tax_amt >= 0
                        ? DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => 210300, 'Dr' => $tax_amt,      'Trace' => 2111]))
                        : DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => 210300, 'Cr' => abs($tax_amt), 'Trace' => 2110]));
                }
            }

            // 4. Bank charges
            $bank_charges = floatval($request->input('bank_charges') ?? 0);
            if ($bank_charges > 0) {
                $bank_note = 'Bank Charges Applied from ' . $request->input('bank_name') . ' at rate of ' . $request->input('vat_per') . '%';
                DB::table('journal')->insert(['VHNO' => $type_code.$vhno, 'JournalType' => $type_code, 'ChartOfAccountID' => '110400', 'PartyID' => $party_id, 'InvoiceMasterID' => $tourmaster_id, 'Date' => $date, 'Dr' => $bank_charges, 'Narration' => $bank_note, 'Trace' => 1203]);
                DB::table('journal')->insert(['VHNO' => $type_code.$vhno, 'JournalType' => $type_code, 'ChartOfAccountID' => '210318', 'PartyID' => $party_id, 'InvoiceMasterID' => $tourmaster_id, 'Date' => $date, 'Cr' => $bank_charges, 'Narration' => $bank_note, 'Trace' => 1204]);
            }

            DB::commit();

            return redirect('tour-booking')
                ->with('error', 'Tour Booking Saved Successfully')
                ->with('class', 'success')
                ->with('tourMasterID', $tourmaster_id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->with('class', 'danger');
        }
    }

    // ============================================================
    // EDIT
    // ============================================================
    public function edit($id)
    {
        Session::put('menu', 'Invoice');

        $invoice_type = DB::table('invoice_type')->whereIn('InvoiceTypeID', [1, 2])->get();
        $items        = DB::table('item')->where('ItemType', 'I')->get();
        $supplier     = DB::table('supplier')->get();
        $party        = DB::table('party')->get();

        $query = DB::table('user')->where('Active', 'Yes');
        if (Session::get('UserType') !== 'Admin') {
            $query->where('UserID', Session::get('UserID'));
        }
        $saleman = $query->get();

        $master = DB::table('tour_booking_master')->where('id', $id)->first();

        if (!$master) {
            return redirect('tour-booking')->with('error', 'Booking not found')->with('class', 'danger');
        }

        $details = DB::table('tour_booking_details as tb')
            ->leftJoin('item as i',     'i.ItemID',     '=', 'tb.item_id')
            ->leftJoin('supplier as s', 's.SupplierID', '=', 'tb.supplier_id')
            ->select('tb.*', 'i.ItemName', 's.SupplierName')
            ->where('tb.tourmaster_id', $id)
            ->get();

        $invoicetypes = DB::table('invoice_type')->get();

        return view('tour_booking.edit', compact(
            'invoice_type', 'items', 'supplier', 'party', 'saleman', 'master', 'details', 'invoicetypes'
        ));
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $invoice_type = DB::table('invoice_type')->where('InvoiceTypeID', $request->input('InvoiceTypeID'))->first();
            $type_code    = $invoice_type->InvoiceTypeCode;
            $vhno         = $request->input('VHNO');
            $party_id     = $request->input('party_id');
            $date         = $request->input('inv_date');

            // 1. Update master
            DB::table('tour_booking_master')->where('id', $id)->update([
                'invoicetype_id' => $request->input('InvoiceTypeID'),
                'party_id'       => $party_id,
                'inv_date'       => $date,
                'due_date'       => $request->input('due_date'),
                'reference_no'   => $request->input('reference_no'),
                'salesman_id'    => $request->input('salesman_id'),
                'source'         => $request->input('source'),
                'note'           => $request->input('note'),
                'Payment_mode'   => $request->input('bank_name')    ?? '',
                'bank_name'      => $request->input('bank_name')    ?? '',
                'vat_per'        => $request->input('vat_per')      ?? 0,
                'bank_charges'   => $request->input('bank_charges') ?? 0,
                'total'          => $request->input('total')        ?? 0,
                'grandtotal'     => $request->input('grandtotal')   ?? 0,
                'paid'           => $request->input('paid')         ?? 0,
                'balance'        => $request->input('balance')      ?? 0,
                'updated_at'     => now(),
            ]);

            // 2. Log
            DB::table('log')->insert([
                'UserName'  => Session::get('FullName'),
                'Amount'    => $request->input('total'),
                'Date'      => date('Y-m-d H:i:s'),
                'Section'   => 'Tour Booking Update',
                'VHNO'      => $id,
                'Narration' => 'Tour Booking Updated',
                'Trace'     => 102,
                'UserID'    => Session::get('UserID'),
            ]);

            // 3. Wipe old details and journals then re-insert fresh
            DB::table('tour_booking_details')->where('tourmaster_id', $id)->delete();
            DB::table('journal')->where('InvoiceMasterID', $id)->where('JournalType', $type_code)->delete();

            // 4. Re-insert line items
            $item_ids = $request->input('item_id', []);

            for ($i = 0; $i < count($item_ids); $i++) {

                if (empty($item_ids[$i])) continue;

                $qty        = intval($request->quantity[$i]    ?? 1);
                $fare       = floatval($request->fare[$i]       ?? 0);
                $total_fare = floatval($request->total_fare[$i] ?? ($fare * $qty));
                $tax_amt    = floatval($request->tax_amount[$i] ?? 0);
                $service    = floatval($request->service[$i]    ?? 0);
                $row_total  = floatval($request->row_total[$i]  ?? 0);
                $sup_id     = $request->supplier_id[$i]         ?? null;
                $pax_name   = $request->pax_name[$i]            ?? '';

                DB::table('tour_booking_details')->insert([
                    'tourmaster_id'  => $id,
                    'item_id'        => $item_ids[$i],
                    'supplier_id'    => $sup_id,
                    'quantity'       => $qty,
                    'pax_name'       => $pax_name,
                    'contact'        => $request->contact[$i]        ?? null,
                    'departure_date' => !empty($request->departure_date[$i]) ? $request->departure_date[$i] : null,
                    'pick_point'     => $request->pick_point[$i]     ?? null,
                    'ref_no'         => $request->ref_no[$i]         ?? null,
                    'fare'           => $fare,
                    'total_fare'     => $total_fare,
                    'tax_per'        => $request->tax_per[$i]        ?? 0,
                    'tax_amount'     => $tax_amt,
                    'service'        => $service,
                    'total'          => $row_total,
                    'paid'           => 0,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                $j = [
                    'VHNO'            => $type_code . $vhno,
                    'JournalType'     => $type_code,
                    'SupplierID'      => $sup_id,
                    'PartyID'         => $party_id,
                    'InvoiceMasterID' => $id,
                    'Date'            => $date,
                    'Narration'       => $pax_name,
                ];

                if ($request->input('InvoiceTypeID') == 1) {
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '110400', 'Dr' => $row_total,  'Trace' => 105]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '510103', 'Cr' => $total_fare, 'Trace' => 106]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '510103', 'Dr' => $total_fare, 'Trace' => 109]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '210100', 'Cr' => $total_fare, 'Trace' => 110]));
                    $service >= 0
                        ? DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '410101', 'Cr' => $service,      'Trace' => 107]))
                        : DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '410101', 'Dr' => abs($service), 'Trace' => 108]));
                    $tax_amt >= 0
                        ? DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => 210300, 'Cr' => $tax_amt,      'Trace' => 111]))
                        : DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => 210300, 'Dr' => abs($tax_amt), 'Trace' => 111]));
                }

                if ($request->input('InvoiceTypeID') == 2) {
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '110400', 'Cr' => $row_total,  'Trace' => 201]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '510103', 'Dr' => $total_fare, 'Trace' => 204]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '510103', 'Cr' => $total_fare, 'Trace' => 205]));
                    DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '210100', 'Dr' => $total_fare, 'Trace' => 206]));
                    $service >= 0
                        ? DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '410101', 'Dr' => $service,      'Trace' => 202]))
                        : DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => '410101', 'Cr' => abs($service), 'Trace' => 2022]));
                    $tax_amt >= 0
                        ? DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => 210300, 'Dr' => $tax_amt,      'Trace' => 2111]))
                        : DB::table('journal')->insert(array_merge($j, ['ChartOfAccountID' => 210300, 'Cr' => abs($tax_amt), 'Trace' => 2110]));
                }
            }

            // 5. Bank charges
            $bank_charges = floatval($request->input('bank_charges') ?? 0);
            if ($bank_charges > 0) {
                $bank_note = 'Bank Charges Applied from ' . $request->input('bank_name') . ' at rate of ' . $request->input('vat_per') . '%';
                DB::table('journal')->insert(['VHNO' => $type_code.$vhno, 'JournalType' => $type_code, 'ChartOfAccountID' => '110400', 'PartyID' => $party_id, 'InvoiceMasterID' => $id, 'Date' => $date, 'Dr' => $bank_charges, 'Narration' => $bank_note, 'Trace' => 1203]);
                DB::table('journal')->insert(['VHNO' => $type_code.$vhno, 'JournalType' => $type_code, 'ChartOfAccountID' => '210318', 'PartyID' => $party_id, 'InvoiceMasterID' => $id, 'Date' => $date, 'Cr' => $bank_charges, 'Narration' => $bank_note, 'Trace' => 1204]);
            }

            DB::commit();

            return redirect('tour-booking')
                ->with('error', 'Tour Booking Updated Successfully')
                ->with('class', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->with('class', 'danger');
        }
    }

    // ============================================================
    // DELETE
    // ============================================================
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $master       = DB::table('tour_booking_master')->where('id', $id)->first();
            $invoice_type = DB::table('invoice_type')->where('InvoiceTypeID', $master->invoicetype_id)->first();

            DB::table('tour_booking_details')->where('tourmaster_id', $id)->delete();
            DB::table('journal')->where('InvoiceMasterID', $id)->where('JournalType', $invoice_type->InvoiceTypeCode)->delete();
            DB::table('tour_booking_master')->where('id', $id)->delete();

            DB::commit();

            return response()->json(['message' => 'Tour Booking deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ============================================================
    // PDF
    // ============================================================
    public function TourBookingPDF($id, $download = null)
    {
        try {
            Session::put('menu', 'Invoice');

            $company = DB::table('company')->where('CompanyID', 1)->first();

            $invoice_mst = DB::table('tour_booking_master as tbm')
                ->leftJoin('party as p',         'p.PartyID',        '=', 'tbm.party_id')
                ->leftJoin('invoice_type as it',  'it.InvoiceTypeID', '=', 'tbm.invoicetype_id')
                ->select(
                    'tbm.*',
                    'p.PartyName',
                    'p.Phone',
                    'p.TRN',
                    'it.InvoiceTypeCode',
                    'it.InvoiceType'
                )
                ->where('tbm.id', $id)
                ->first();

            if (!$invoice_mst) {
                return redirect('tour-booking')->with('error', 'Booking not found')->with('class', 'danger');
            }

            $invoice_det = DB::table('tour_booking_details as tb')
                ->leftJoin('item as i',     'i.ItemID',     '=', 'tb.item_id')
                ->leftJoin('supplier as s', 's.SupplierID', '=', 'tb.supplier_id')
                ->select('tb.*', 'i.ItemName', 's.SupplierName')
                ->where('tb.tourmaster_id', $id)
                ->get();

            $balance  = $invoice_mst->grandtotal - $invoice_mst->paid;
            $filename = 'TourBooking-' . $invoice_mst->invoice_no . '-' . $invoice_mst->inv_date . '-' . $invoice_mst->PartyName;

            $pdf = PDF::loadView('tour_booking.invoice', compact(
                'invoice_mst', 'invoice_det', 'company', 'balance'
            ));
            $pdf->setPaper('A4', 'portrait');

            if ($download == 'download') {
                return $pdf->download($filename . '.pdf');
            }

            return $pdf->stream();

        } catch (\Exception $e) {
            return response('PDF Error: ' . $e->getMessage(), 500);
        }
    }

    public function TourBookingPDFView($id)
    {
        Session::put('menu', 'Invoice');

        $company = DB::table('company')->where('CompanyID', 1)->first();

        $invoice_mst = DB::table('tour_booking_master as tbm')
            ->leftJoin('party as p',        'p.PartyID',        '=', 'tbm.party_id')
            ->leftJoin('invoice_type as it', 'it.InvoiceTypeID', '=', 'tbm.invoicetype_id')
            ->select(
                'tbm.*',
                'p.PartyName',
                'p.Phone',
                'p.TRN',
                'it.InvoiceTypeCode',
                'it.InvoiceType'
            )
            ->where('tbm.id', $id)
            ->first();

        if (!$invoice_mst) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $invoice_det = DB::table('tour_booking_details as tb')
            ->leftJoin('item as i',     'i.ItemID',     '=', 'tb.item_id')
            ->leftJoin('supplier as s', 's.SupplierID', '=', 'tb.supplier_id')
            ->select('tb.*', 'i.ItemName', 's.SupplierName')
            ->where('tb.tourmaster_id', $id)
            ->get();

        $balance = $invoice_mst->grandtotal - $invoice_mst->paid;

        $html = view('tour_booking.invoice', compact(
            'invoice_mst', 'invoice_det', 'company', 'balance'
        ))->render();

        return response()->json(['html' => $html], 200);
    }

    public function tourBookingPaymentSave(Request $request)
    {
        $request->validate([
            'voucher_number'   => 'required',
            'deposit_to'       => 'required',
            'ChartOfAccountID' => 'required',
            'partyID'          => 'required',
            'InvoiceMasterID'  => 'required',  // this is tourmaster_id
            'amount_received'  => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            // Determine voucher type and Dr/Cr direction based on invoice type
            if ($request->InvoiceTypeID == 1) {
                $VoucherType = ($request->payment_mode == 'CASH') ? 5 : 2;
                $acc_company = 'Debit';
                $acc_party   = 'Credit';
            } else {
                $VoucherType = ($request->payment_mode == 'CASH') ? 4 : 1;
                $acc_company = 'Credit';
                $acc_party   = 'Debit';
            }

            $tourmaster_id  = $request->InvoiceMasterID;
            $voucher_number = $request->input('voucher_number');
            $amount         = floatval($request->input('amount_received'));
            $bank_charges   = floatval($request->input('bank_charges') ?? 0);
            $party_id       = $request->input('partyID');
            $date           = dateformatpc($request->Date);
            $notes          = $request->notes;

            // ── 1. Update tour_booking_master payment mode & voucher ──────
            DB::table('tour_booking_master')->where('id', $tourmaster_id)->update([
                'Payment_mode' => $request->input('payment_mode'),
                'voucher'      => $voucher_number,
                'updated_at'   => now(),
            ]);

            // ── 2. Log ────────────────────────────────────────────────────
            DB::table('log')->insert([
                'UserName'  => Session::get('FullName'),
                'Amount'    => $amount,
                'Date'      => date('Y-m-d H:i:s'),
                'Section'   => 'Tour Booking',
                'VHNO'      => $tourmaster_id,
                'Narration' => 'Tour Booking Payment recorded, voucher: ' . $voucher_number,
                'Trace'     => 1,
                'UserID'    => Session::get('UserID'),
            ]);

            // ── 3. Create voucher master ───────────────────────────────────
            $voucher_mst_id = DB::table('voucher_master')->insertGetId([
                'VoucherCodeID' => $VoucherType,
                'Voucher'       => $voucher_number,
                'Narration'     => $notes,
                'Date'          => $date,
            ]);

            // ── 4. Voucher detail — Company account (Deposit To) ──────────
            DB::table('voucher_detail')->insert([
                'VoucherMstID' => $voucher_mst_id,
                'Voucher'      => $voucher_number,
                'Date'         => $date,
                'ChOfAcc'      => $request->input('deposit_to'),
                'PartyID'      => $party_id,
                'Narration'    => $notes,
                'InvoiceNo'    => $tourmaster_id,
                $acc_company   => $amount,
            ]);

            // ── 5. Voucher detail — A/R account (110400) ──────────────────
            DB::table('voucher_detail')->insert([
                'VoucherMstID' => $voucher_mst_id,
                'Voucher'      => $voucher_number,
                'Date'         => $date,
                'ChOfAcc'      => 110400,
                'PartyID'      => $party_id,
                'Narration'    => $notes,
                'InvoiceNo'    => $tourmaster_id,
                $acc_party     => $amount,
            ]);

            // ── 6. Bank charges entries (if any) ──────────────────────────
            if ($bank_charges > 0) {

                // DR: deposit_to account
                DB::table('voucher_detail')->insert([
                    'VoucherMstID' => $voucher_mst_id,
                    'Voucher'      => $voucher_number,
                    'Date'         => $date,
                    'ChOfAcc'      => $request->deposit_to,
                    'PartyID'      => $party_id,
                    'Narration'    => $notes . ' - Bank Charges',
                    'InvoiceNo'    => $tourmaster_id,
                    'Debit'        => $bank_charges,
                ]);

                // CR: A/R account (110400)
                DB::table('voucher_detail')->insert([
                    'VoucherMstID' => $voucher_mst_id,
                    'Voucher'      => $voucher_number,
                    'Date'         => $date,
                    'ChOfAcc'      => 110400,
                    'PartyID'      => $party_id,
                    'Narration'    => $notes . ' - Bank Charges',
                    'InvoiceNo'    => $tourmaster_id,
                    'Credit'       => $bank_charges,
                ]);

                // DR: Bank charges expense (210318)
                DB::table('voucher_detail')->insert([
                    'VoucherMstID' => $voucher_mst_id,
                    'Voucher'      => $voucher_number,
                    'Date'         => $date,
                    'ChOfAcc'      => 210318,
                    'PartyID'      => $party_id,
                    'Narration'    => $notes . ' - Bank Charges',
                    'InvoiceNo'    => $tourmaster_id,
                    'Debit'        => $bank_charges,
                ]);

                // CR: deposit_to account
                DB::table('voucher_detail')->insert([
                    'VoucherMstID' => $voucher_mst_id,
                    'Voucher'      => $voucher_number,
                    'Date'         => $date,
                    'ChOfAcc'      => $request->deposit_to,
                    'PartyID'      => $party_id,
                    'Narration'    => $notes . ' - Bank Charges',
                    'InvoiceNo'    => $tourmaster_id,
                    'Credit'       => $bank_charges,
                ]);
            }

            // ── 7. Update paid & balance on tour_booking_master ───────────
            // Sum all payments from voucher_detail for this tour booking
            $total_paid = DB::table('voucher_detail')
                ->where('InvoiceNo', $tourmaster_id)
                ->where('ChOfAcc', 110400)
                ->sum($acc_party == 'Credit' ? 'Credit' : 'Debit');

            $master = DB::table('tour_booking_master')->where('id', $tourmaster_id)->first();

            DB::table('tour_booking_master')->where('id', $tourmaster_id)->update([
                'paid'       => $total_paid,
                'balance'    => $master->grandtotal - $total_paid,
                'updated_at' => now(),
            ]);

            // ── 8. Log completion ─────────────────────────────────────────
            DB::table('log')->insert([
                'UserName'  => Session::get('FullName'),
                'Amount'    => $amount,
                'Date'      => date('Y-m-d H:i:s'),
                'Section'   => 'Tour Booking',
                'VHNO'      => $tourmaster_id,
                'Narration' => 'Tour Booking paid/balance updated. Paid: ' . $total_paid,
                'Trace'     => 5,
                'UserID'    => Session::get('UserID'),
            ]);

            // ── 9. Handle file uploads ────────────────────────────────────
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $file->store('uploads', 'public');
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('error', 'Payment recorded successfully!')
                ->with('class', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->with('class', 'danger');
        }
    }

    public function getTourBookingPaymentInfo($id)
    {
        $master = DB::table('tour_booking_master as tbm')
            ->leftJoin('party as p',         'p.PartyID',        '=', 'tbm.party_id')
            ->leftJoin('invoice_type as it',  'it.InvoiceTypeID', '=', 'tbm.invoicetype_id')
            ->select(
                'tbm.id as InvoiceMasterID',
                'tbm.grandtotal as Total',
                'tbm.paid as Paid',
                'tbm.balance as Balance',
                'tbm.bank_charges as BankCharges',
                'tbm.Payment_mode as PaymentMode',
                'tbm.voucher as Voucher',
                'tbm.invoicetype_id as InvoiceTypeID',
                'p.PartyName',
                'p.PartyID',
                'it.InvoiceType',
                'it.InvoiceTypeCode'
            )
            ->where('tbm.id', $id)
            ->first();

        if (!$master) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($master);
    }
}