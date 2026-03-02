<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TravelBookingController extends Controller
{
    public function index(Request $request)
    {
        Session::put('menu', 'Invoice');
        $pagetitle = 'Invoice';

        $allow = check_role(Session::get('UserID'), 'Invoice', 'List');
        if ($allow[0]->Allow == 'N') {
            return redirect()->back()
                ->with('error', 'You access is limited')
                ->with('class', 'danger');
        }

        Session()->forget('LeadID');
        Session()->forget('PartyID');

        /*
        |--------------------------------------------------------------------------
        | AJAX REQUEST (DATATABLE)
        |--------------------------------------------------------------------------
        */
        if ($request->ajax()) {

            $query = DB::table('v_invoice_detail')
                ->whereNotIn('ItemCode', ['UB', 'UA']);

            // Non Admin restriction
            if (Session::get('UserType') !== 'Admin') {
                $query->where('UserID', Session::get('UserID'));
            }

            // Party Name Filter
            if ($request->filled('party_name')) {
                $query->where('PartyName', 'like', '%' . $request->party_name . '%');
            }

            // Phone Filter (last 9 digits match)
            if ($request->filled('Phone')) {
                $clean_phone = str_replace(' ', '', trim($request->Phone));
                $last_9_digits = substr($clean_phone, -9);

                $query->where(function ($subQuery) use ($last_9_digits) {
                    $subQuery->where(
                        DB::raw("REPLACE(Phone,' ','')"),
                        'like',
                        '%' . $last_9_digits . '%'
                    );
                });
            }

            // Date Filters
            if ($request->filled('startdate')) {
                $query->whereDate('Date', '>=', $request->startdate);
            }

            if ($request->filled('enddate')) {
                $query->whereDate('Date', '<=', $request->enddate);
            }

            // Extra Filters
            if ($request->filled('UserID')) {
                $query->where('UserID', $request->UserID);
            }

            if ($request->filled('ItemID')) {
                $query->where('ItemID', $request->ItemID);
            }

            $data = $query->orderBy('InvoiceMasterID')->get();

            $PaymentMode = DB::table('chartofaccount')
                ->distinct()
                ->pluck('category');

            return Datatables::of($data)
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
                                   onclick="openLedgerModal(' . $row->PartyID . ', \'' . $row->PartyName . '\')" 
                                   class="dropdown-item">
                                   <i class="mdi mdi-account-details me-1" style="color:#FF5733;"></i>
                                   <strong>Party Ledger</strong>
                                </a>
                            </li>

                            <div class="dropdown-divider"></div>

                            <a href="' . URL('/InvoiceRefund/' . $row->InvoiceMasterID) . '" class="dropdown-item">
                                <i class="mdi mdi-backup-restore text-danger me-1"></i>
                                Invoice Refund
                            </a>

                            <div class="dropdown-divider"></div>

                            <li>
                                <a href="#" class="dropdown-item record-payment"
                                   data-invoicemasterid="' . $row->InvoiceMasterID . '"
                                   data-invoicetypeid="' . $row->InvoiceTypeID . '">
                                   <i class="mdi mdi-cash-usd text-success me-1"></i>
                                   Record Payment
                                </a>
                            </li>

                            <div class="dropdown-divider"></div>

                            <li>
                                <a href="javascript:void(0)" 
                                   onclick="loadPDF(\'' . URL('/InvoicePDFView/' . $row->InvoiceMasterID) . '\')" 
                                   class="dropdown-item">
                                   <i class="mdi mdi-file-pdf me-1" style="color:#FF5733;"></i>
                                   View Invoice
                                </a>
                            </li>

                            <a href="' . URL('/InvoiceEdit/' . $row->InvoiceMasterID) . '" class="dropdown-item">
                                <i class="bx bx-pencil text-secondary me-1"></i>
                                Edit Invoice
                            </a>

                            <li>
                                <a href="' . URL('/InvoicePDF/' . $row->InvoiceMasterID) . '/download" target="_blank" class="dropdown-item">
                                    <i class="mdi mdi-file-pdf-outline me-1" style="color:#AF0505;"></i>
                                    Download Invoice
                                </a>
                            </li>

                            <li>
                                <a href="' . URL('/InvoicePDF/' . $row->InvoiceMasterID) . '" target="_blank" class="dropdown-item">
                                    <i class="mdi mdi-eye-outline text-secondary me-1"></i>
                                    View PDF
                                </a>
                            </li>

                            <li>
                                <a href="javascript:void(0)" 
                                   onclick="delete_invoice(' . $row->InvoiceMasterID . ')" 
                                   class="dropdown-item">
                                   <i class="bx bx-trash text-danger me-1"></i>
                                   Delete Invoice
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>';
                })
                ->rawColumns(['action'])
                ->with('paymentModes', $PaymentMode)
                ->make(true);
        }

        /*
    |--------------------------------------------------------------------------
    | NORMAL PAGE LOAD
    |--------------------------------------------------------------------------
    */
        $chartofaccount = DB::table('chartofaccount')
            ->where('ChartOfAccountID', '560110')
            ->get();

        return view('invoice', compact('pagetitle', 'chartofaccount'));
    }
}
