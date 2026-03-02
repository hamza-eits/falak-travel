@extends('tmp')

@section('title', 'Tour Booking')

@section('content')

{{-- ============================================================
     STYLES
     ============================================================ --}}
<style>
    :root {
        --primary:       #2563eb;
        --primary-dark:  #1d4ed8;
        --primary-light: #eff6ff;
        --danger:        #dc2626;
        --success:       #16a34a;
        --border:        #e2e8f0;
        --text-main:     #1e293b;
        --text-muted:    #64748b;
        --bg-section:    #f8fafc;
        --radius:        6px;
        --shadow-card:   0 1px 4px rgba(0,0,0,.08), 0 0 0 1px rgba(0,0,0,.04);
    }

    /* --- Base --- */
    body { background: #f1f5f9; color: var(--text-main); }

    /* --- Card --- */
    .tour-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.25rem;
    }
    .tour-card-header {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        font-weight: 600;
        font-size: .85rem;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--text-muted);
    }
    .tour-card-header i { font-size: 1rem; color: var(--primary); }
    .tour-card-body { padding: 1.25rem; }

    /* --- Form elements --- */
    .form-label {
        display: block;
        font-size: .78rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: .35rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .form-control,
    .form-select {
        border-radius: var(--radius) !important;
        border: 1px solid var(--border);
        font-size: .875rem;
        color: var(--text-main);
        padding: .45rem .75rem;
        transition: border-color .15s, box-shadow .15s;
        width: 100%;
    }
    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        outline: none;
    }
    .input-prefix {
        display: flex;
        align-items: center;
    }
    .input-prefix .prefix-text {
        background: var(--bg-section);
        border: 1px solid var(--border);
        border-right: none;
        border-radius: var(--radius) 0 0 var(--radius);
        padding: .45rem .65rem;
        font-size: .8rem;
        font-weight: 600;
        color: var(--text-muted);
        white-space: nowrap;
    }
    .input-prefix .form-control {
        border-radius: 0 var(--radius) var(--radius) 0 !important;
    }

    /* --- Select2 overrides --- */
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single {
        height: 36px;
        border: 1px solid var(--border);
        border-radius: var(--radius) !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        padding-left: .75rem;
        font-size: .875rem;
        color: var(--text-main);
    }
    .select2-container .select2-selection--single .select2-selection__arrow { height: 34px; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background: var(--primary); }
    .select2-dropdown { border: 1px solid var(--border); border-radius: var(--radius); box-shadow: 0 4px 16px rgba(0,0,0,.1); }
    .select2-search--dropdown { padding: 6px; }
    .select2-search--dropdown .select2-search__field { border: 1px solid var(--border); border-radius: 4px; padding: 4px 8px; }

    /* --- Line items table --- */
    .line-items-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
    .line-items-table thead th {
        background: var(--bg-section);
        border: 1px solid var(--border);
        padding: .55rem .6rem;
        font-weight: 600;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--text-muted);
        white-space: nowrap;
    }
    .line-items-table tbody td {
        border: 1px solid var(--border);
        padding: .4rem .4rem;
        vertical-align: top;
    }
    .line-items-table tbody tr:hover { background: var(--primary-light); }
    .line-items-table .form-control,
    .line-items-table .form-select { font-size: .8rem; padding: .35rem .5rem; margin-bottom: .3rem; }
    .line-items-table .form-control:last-child,
    .line-items-table .form-select:last-child { margin-bottom: 0; }

    /* --- Totals panel --- */
    .totals-panel { background: var(--bg-section); border-radius: var(--radius); padding: 1rem; }
    .totals-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .45rem 0;
        border-bottom: 1px dashed var(--border);
        font-size: .875rem;
    }
    .totals-row:last-child { border-bottom: none; }
    .totals-row .label { color: var(--text-muted); font-weight: 500; }
    .totals-row .value { font-weight: 600; font-family: 'Courier New', monospace; }
    .totals-row.grand { font-size: 1rem; color: var(--primary); }

    /* --- Bank charges row --- */
    .bank-charges-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .5rem; }

    /* --- Buttons --- */
    .btn { border-radius: var(--radius); font-size: .85rem; font-weight: 600; padding: .45rem 1.1rem; }
    .btn-primary { background: var(--primary); border-color: var(--primary); }
    .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
    .btn-success-custom { background: var(--success); border-color: var(--success); color: #fff; }
    .btn-success-custom:hover { background: #15803d; }
    .btn-row-add { background: #f0fdf4; border: 1px dashed #86efac; color: var(--success); }
    .btn-row-add:hover { background: #dcfce7; }
    .btn-row-del { background: #fef2f2; border: 1px solid #fca5a5; color: var(--danger); }
    .btn-row-del:hover { background: #fee2e2; }

    /* --- Page header --- */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }
    .page-header h4 { font-size: 1.1rem; font-weight: 700; margin: 0; }
    .page-header .breadcrumb { font-size: .8rem; color: var(--text-muted); margin: 0; }

    /* --- Status badge --- */
    .status-badge {
        display: inline-block;
        padding: .2rem .65rem;
        border-radius: 99px;
        font-size: .72rem;
        font-weight: 600;
    }
    .status-badge.new { background: #eff6ff; color: var(--primary); }

    /* --- Error/validation --- */
    .field-error { font-size: .75rem; color: var(--danger); display: none; margin-top: .2rem; }
    .field-error.show { display: block; }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .bank-charges-grid { grid-template-columns: 1fr; }
        .line-items-table { min-width: 900px; }
        .table-scroll { overflow-x: auto; }
    }
</style>

{{-- ============================================================
     ADD CUSTOMER MODAL
     ============================================================ --}}
<div class="modal fade" id="modalAddCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content" style="border-radius:10px;border:none;box-shadow:0 8px 32px rgba(0,0,0,.15)">
            <div class="modal-header" style="border-bottom:1px solid var(--border);padding:1rem 1.25rem">
                <h6 class="modal-title fw-bold mb-0"><i class="bx bx-user-plus me-1 text-primary"></i> Add New Customer</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="newPartyName" placeholder="Enter full name">
                    <div class="field-error" id="err-party-name">Name is required.</div>
                </div>
                <div class="mb-1">
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="newPartyPhone" placeholder="e.g. 05xxxxxxxx">
                    <div class="field-error" id="err-party-phone">Phone number is required.</div>
                    <div class="field-error" id="err-phone-exists">This phone number already exists.</div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border);padding:.75rem 1.25rem">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnSaveCustomer"><i class="bx bx-save me-1"></i> Save Customer</button>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            @if (session('error'))
                <div class="alert alert-{{ session('class') }} alert-dismissible fade show p-2 mb-3" role="alert">
                    {{ session('error') }}<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger p-2 mb-3">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach ($errors->all() as $error)<li style="font-size:.85rem">{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="page-header">
                <div>
                    <h4><i class="bx bx-trip me-1 text-primary"></i> New Tour Booking</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ URL('/tour-booking') }}" style="font-size:.8rem;color:var(--primary)">Tour Bookings</a></li>
                            <li class="breadcrumb-item active" style="font-size:.8rem">Create</li>
                        </ol>
                    </nav>
                </div>
                <span class="status-badge new">Draft</span>
            </div>

            <form action="{{ URL('/tour-booking/save') }}" method="POST" id="tourForm">
                @csrf

                {{-- BOOKING INFORMATION --}}
                <div class="tour-card">
                    <div class="tour-card-header"><i class="bx bx-info-circle"></i> Booking Information</div>
                    <div class="tour-card-body">
                        <div class="row g-3">

                            <div class="col-md-3">
                                <label class="form-label">Invoice Type <span class="text-danger">*</span></label>
                                <select name="InvoiceTypeID" id="InvoiceTypeID" class="form-select" required>
                                    @foreach ($invoice_type as $type)
                                        <option value="{{ $type->InvoiceTypeID }}">{{ $type->InvoiceTypeCode }} - {{ $type->InvoiceType }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Invoice #</label>
                                <input type="text" class="form-control ro" name="VHNO" value="{{ $vhno[0]->VHNO }}" readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="inv_date" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" name="due_date" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Customer / Party <span class="text-danger">*</span></label>
                                <select name="party_id" id="PartyID" class="form-select" required></select>
                                <div class="field-error" id="err-party">Please select a customer.</div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Salesman <span class="text-danger">*</span></label>
                                <select name="salesman_id" id="SalemanID" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    @foreach ($saleman as $user)
                                        <option value="{{ $user->UserID }}">{{ $user->FullName }}</option>
                                    @endforeach
                                </select>
                                <div class="field-error" id="err-salesman">Please select a salesman.</div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Source</label>
                                <input type="text" class="form-control" name="source" placeholder="e.g. Walk-in">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- LINE ITEMS --}}
                <div class="tour-card">
                    <div class="tour-card-header"><i class="bx bx-list-ul"></i> Tour Details / Passengers</div>
                    <div class="tour-card-body p-0">
                        <div class="table-scroll" style="overflow-x:auto;padding:1rem">
                            <table class="line-items-table" id="lineItemsTable" style="min-width:1050px">
                                <thead>
                                    <tr>
                                        <th width="2%"><input type="checkbox" id="checkAll"></th>
                                        <th width="14%">Item / Supplier</th>
                                        <th width="10%">Leader Name / Contact</th>
                                        <th width="9%">Dep. Date / Pick Point</th>
                                        <th width="10%">Qty / Ref No</th>
                                        <th width="8%">Fare (per pax)</th>
                                        <th width="7%">Total Fare</th>
                                        <th width="7%">VAT % / Amt</th>
                                        <th width="7%">Service</th>
                                        <th width="7%">Total (customer pays)</th>
                                    </tr>
                                </thead>
                                <tbody id="lineItemsBody"></tbody>
                            </table>
                        </div>
                        <div class="d-flex align-items-center gap-2 px-4 pb-4">
                            <button type="button" class="btn btn-sm btn-row-add" id="btnAddRow"><i class="bx bx-plus me-1"></i> Add Row</button>
                            <button type="button" class="btn btn-sm btn-row-del" id="btnDeleteRows"><i class="bx bx-trash me-1"></i> Delete Selected</button>
                        </div>
                    </div>
                </div>

                {{-- NOTES + TOTALS --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="tour-card h-100">
                            <div class="tour-card-header"><i class="bx bx-note"></i> Notes</div>
                            <div class="tour-card-body">
                                <textarea class="form-control" name="remarks" rows="5" placeholder="Any additional notes..." style="resize:vertical"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="tour-card">
                            <div class="tour-card-header"><i class="bx bx-calculator"></i> Summary</div>
                            <div class="tour-card-body">
                                <div class="totals-panel">
                                    <div class="totals-row">
                                        <span class="label">Subtotal</span>
                                        <span class="value">AED <span id="displaySubTotal">0.00</span></span>
                                    </div>
                                    <div style="padding:.6rem 0;border-bottom:1px dashed var(--border)">
                                        <div class="label mb-2">Bank Charges</div>
                                        <div class="bank-charges-grid">
                                            <select name="bank_name" class="form-select form-select-sm">
                                                <option value="">Provider</option>
                                                <option value="Nomod">Nomod</option>
                                                <option value="Tabbay">Tabbay</option>
                                                <option value="Tamara">Tamara</option>
                                            </select>
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="vat_per" id="bankPct" class="form-control" placeholder="%" step="0.01" min="0" max="100" value="0">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">AED</span>
                                                <input type="number" name="bank_charges" id="bankAmt" class="form-control" placeholder="Amount" step="0.01" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="totals-row">
                                        <span class="label">Total</span>
                                        <span class="value">AED <span id="displayTotal">0.00</span></span>
                                    </div>
                                    <div class="totals-row grand">
                                        <span class="label fw-bold">Grand Total</span>
                                        <span class="value fw-bold">AED <span id="displayGrandTotal">0.00</span></span>
                                    </div>
                                    <div class="totals-row">
                                        <span class="label">Amount Due</span>
                                        <span class="value text-danger">AED <span id="displayAmountDue">0.00</span></span>
                                    </div>
                                </div>
                                <input type="hidden" name="total"      id="hiddenTotal"      value="0">
                                <input type="hidden" name="grandtotal" id="hiddenGrandTotal" value="0">
                                <input type="hidden" name="balance"    id="hiddenBalance"    value="0">
                                <input type="hidden" name="paid"       value="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="tour-card">
                    <div class="tour-card-body d-flex align-items-center justify-content-end gap-2">
                        <a href="{{ URL('/tour-booking') }}" class="btn btn-secondary"><i class="bx bx-arrow-back me-1"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save Booking</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
var ITEM_OPTS = '<option value="">-- Select Item --</option>'
    @foreach ($items as $item)
    + '<option value="{{ $item->ItemID }}" data-tax="{{ $item->Percentage ?? 0 }}">{{ addslashes($item->ItemCode) }} - {{ addslashes($item->ItemName) }}</option>'
    @endforeach
;

var SUPPLIER_OPTS = '<option value="">-- Select Supplier --</option>'
    @foreach ($supplier as $s)
    + '<option value="{{ $s->SupplierID }}">{{ addslashes($s->SupplierName) }}</option>'
    @endforeach
;

function buildRowHTML() {
    var h = '<tr>';

    // Checkbox
    h += '<td style="text-align:center;vertical-align:middle"><input type="checkbox" class="row-check"></td>';

    // Item + Supplier
    h += '<td>';
    h +=   '<select name="item_id[]" class="form-select item-select" required>' + ITEM_OPTS + '</select>';
    h +=   '<select name="supplier_id[]" class="form-select supplier-select" style="margin-top:4px">' + SUPPLIER_OPTS + '</select>';
    h += '</td>';

    // Pax Name + Contact
    h += '<td>';
    h +=   '<input type="text" name="pax_name[]" class="form-control" placeholder="Pax Name">';
    h +=   '<input type="text" name="contact[]" class="form-control" style="margin-top:4px" placeholder="Contact No">';
    h += '</td>';

    // Departure Date + Pick Point
    h += '<td>';
    h +=   '<input type="date" name="departure_date[]" class="form-control">';
    h +=   '<input type="text" name="pick_point[]" class="form-control" style="margin-top:4px" placeholder="Pick Point">';
    h += '</td>';

    // Ref No
    h += '<td>';
    h +=   '<input type="number" name="quantity[]" class="form-control qty-input" value="1" min="1" step="1" style="text-align:center;font-weight:700;color:var(--primary)" placeholder="Qty">';
    h +=   '<input type="text" name="ref_no[]" class="form-control" style="margin-top:4px" placeholder="Ref No">';
    h += '</td>';

    // Fare per pax (editable)
    h += '<td><input type="number" name="fare[]" class="form-control fare-input" step="0.01" placeholder="0.00" required></td>';

    // Total Fare = fare × qty (readonly, auto)
    h += '<td><input type="number" name="total_fare[]" class="form-control total-fare-input ro" step="0.01" placeholder="0.00" readonly></td>';

    // VAT % (auto from item) + VAT Amount (auto)
    h += '<td>';
    h +=   '<input type="number" name="tax_per[]" class="form-control tax-per-input" step="0.01" placeholder="%" readonly style="background:var(--bg-section)">';
    h +=   '<input type="number" name="tax_amount[]" class="form-control tax-amt-input" step="0.01" style="margin-top:4px" placeholder="0.00" readonly style="background:var(--bg-section)">';
    h += '</td>';

    // Service (auto, readonly)
    h += '<td><input type="number" name="service[]" class="form-control service-input" step="0.01" placeholder="0.00" readonly style="background:var(--bg-section)"></td>';

    // Total — user enters what customer pays
    h += '<td><input type="number" name="row_total[]" class="form-control row-total-input" step="0.01" placeholder="Enter total" required></td>';

    h += '</tr>';
    return h;
}

function addRow() {
    var $row = jQuery(buildRowHTML());
    jQuery('#lineItemsBody').append($row);
    $row.find('.item-select').select2({ width: '100%' });
    $row.find('.supplier-select').select2({ width: '100%' });
}

/*
  LOGIC:
  fare per pax × qty  = total_fare  (supplier cost — auto)
  total (customer pays) - total_fare = service  (your profit — auto)
  VAT = (tax% × service) / (100 + tax%)          (auto)
  net service = service - VAT                     (auto)
*/
function recalcRow($row) {
    var invoiceType = parseInt(jQuery('#InvoiceTypeID').val());
    var fare        = parseFloat($row.find('.fare-input').val())       || 0;
    var qty         = parseInt($row.find('.qty-input').val())          || 1;
    var rowTotal    = parseFloat($row.find('.row-total-input').val())  || 0;
    var taxPer      = parseFloat($row.find('.tax-per-input').val())    || 0;

    var totalFare   = fare * qty;
    $row.find('.total-fare-input').val(totalFare.toFixed(2));

    if (invoiceType === 1 && rowTotal > 0) {
        var service = rowTotal - totalFare;
        var taxAmt  = taxPer > 0 ? (taxPer * service) / (100 + taxPer) : 0;
        var netSvc  = service - taxAmt;
        $row.find('.tax-amt-input').val(taxAmt.toFixed(2));
        $row.find('.service-input').val(netSvc.toFixed(2));
    }

    if (invoiceType === 2) {
        $row.find('.row-total-input').val(totalFare.toFixed(2));
        $row.find('.tax-per-input').val(0);
        $row.find('.tax-amt-input').val(0);
        $row.find('.service-input').val(0);
    }
}

function recalcTotals() {
    var subtotal = 0;
    jQuery('.row-total-input').each(function () { subtotal += parseFloat(jQuery(this).val()) || 0; });
    var bankAmt = parseFloat(jQuery('#bankAmt').val()) || 0;
    var grand   = subtotal + bankAmt;
    jQuery('#displaySubTotal').text(subtotal.toFixed(2));
    jQuery('#displayTotal').text(subtotal.toFixed(2));
    jQuery('#displayGrandTotal').text(grand.toFixed(2));
    jQuery('#displayAmountDue').text(grand.toFixed(2));
    jQuery('#hiddenTotal').val(subtotal.toFixed(2));
    jQuery('#hiddenGrandTotal').val(grand.toFixed(2));
    jQuery('#hiddenBalance').val(grand.toFixed(2));
}

function updateGrandTotal() {
    var subtotal = parseFloat(jQuery('#hiddenTotal').val()) || 0;
    var bankAmt  = parseFloat(jQuery('#bankAmt').val()) || 0;
    var grand    = subtotal + bankAmt;
    jQuery('#displayGrandTotal').text(grand.toFixed(2));
    jQuery('#displayAmountDue').text(grand.toFixed(2));
    jQuery('#hiddenGrandTotal').val(grand.toFixed(2));
    jQuery('#hiddenBalance').val(grand.toFixed(2));
}

function openAddCustomer() {
    jQuery('#PartyID').select2('close');
    jQuery('#newPartyName, #newPartyPhone').val('');
    jQuery('#err-party-name, #err-party-phone, #err-phone-exists').removeClass('show');
    jQuery('#btnSaveCustomer').prop('disabled', false);
    jQuery('#modalAddCustomer').modal('show');
}

function saveNewCustomer() {
    var name  = jQuery('#newPartyName').val().trim();
    var phone = jQuery('#newPartyPhone').val().trim();
    var valid = true;
    if (!name)  { jQuery('#err-party-name').addClass('show');  valid = false; } else { jQuery('#err-party-name').removeClass('show'); }
    if (!phone) { jQuery('#err-party-phone').addClass('show'); valid = false; } else { jQuery('#err-party-phone').removeClass('show'); }
    if (!valid) return;
    jQuery('#btnSaveCustomer').prop('disabled', true).text('Saving...');
    jQuery.ajax({
        url: '{{ URL("/ajax_party_save") }}', type: 'POST',
        data: { _token: '{{ csrf_token() }}', PartyName: name, Phone: phone },
        success: function (res) {
            var opt = new Option(res.PartyID + ' - ' + res.PartyName + ' - ' + res.Phone, res.PartyID, true, true);
            jQuery('#PartyID').append(opt).trigger('change');
            jQuery('#modalAddCustomer').modal('hide');
        },
        error: function () { alert('Failed to save customer.'); },
        complete: function () { jQuery('#btnSaveCustomer').prop('disabled', false).html('<i class="bx bx-save me-1"></i> Save Customer'); }
    });
}

window.addEventListener('load', function () {

    if (typeof jQuery === 'undefined') { console.error('jQuery not loaded!'); return; }

    (function ($) {

        $('#PartyID').select2({
            placeholder: 'Search customer by name or phone...',
            allowClear: true,
            ajax: {
                url: '{{ URL("/get-parties") }}',
                dataType: 'json', delay: 250,
                data: function (p) { return { search: p.term }; },
                processResults: function (data) {
                    return { results: data.map(function (p) { return { id: p.PartyID, text: p.PartyID + ' - ' + p.PartyName + ' - ' + p.Phone }; }) };
                },
                cache: true
            },
            language: { noResults: function () { return '<button type="button" class="btn btn-primary btn-sm w-100 mt-1" onclick="openAddCustomer()">+ Add New Customer</button>'; } },
            escapeMarkup: function (m) { return m; }
        });

        addRow();

        $('#btnAddRow').on('click', addRow);

        $('#btnDeleteRows').on('click', function () {
            $('#lineItemsBody .row-check:checked').closest('tr').remove();
            $('#checkAll').prop('checked', false);
            recalcTotals();
        });

        $(document).on('change', '#checkAll', function () {
            $('.row-check').prop('checked', $(this).is(':checked'));
        });

        // Item changed → fill VAT %
        $(document).on('change', '.item-select', function () {
            var tax = $(this).find(':selected').data('tax') || 0;
            $(this).closest('tr').find('.tax-per-input').val(tax);
            recalcRow($(this).closest('tr'));
            recalcTotals();
        });

        // Fare, qty, or total changed → recalc
        $(document).on('input', '.fare-input, .qty-input, .row-total-input', function () {
            recalcRow($(this).closest('tr'));
            recalcTotals();
        });

        $('#bankPct').on('input', function () {
            var sub = parseFloat($('#hiddenTotal').val()) || 0;
            $('#bankAmt').val(((parseFloat($(this).val()) || 0) / 100 * sub).toFixed(2));
            updateGrandTotal();
        });

        $('#bankAmt').on('input', function () {
            var sub = parseFloat($('#hiddenTotal').val()) || 0;
            var amt = parseFloat($(this).val()) || 0;
            $('#bankPct').val(sub > 0 ? ((amt / sub) * 100).toFixed(2) : 0);
            updateGrandTotal();
        });

        $('#tourForm').on('submit', function (e) {
            var valid = true;
            if (!$('#PartyID').val())   { $('#err-party').addClass('show');    valid = false; } else { $('#err-party').removeClass('show'); }
            if (!$('#SalemanID').val()) { $('#err-salesman').addClass('show'); valid = false; } else { $('#err-salesman').removeClass('show'); }
            if (!valid) e.preventDefault();
        });

        $('#btnSaveCustomer').on('click', saveNewCustomer);

        $(document).on('keyup', '#newPartyPhone', function () {
            var phone = $(this).val().trim();
            if (!phone) return;
            $.post('{{ URL("/ajax_party_validate") }}', { _token: '{{ csrf_token() }}', Phone: phone }, function (data) {
                if (data.total > 0) { $('#err-phone-exists').addClass('show'); $('#btnSaveCustomer').prop('disabled', true); }
                else { $('#err-phone-exists').removeClass('show'); $('#btnSaveCustomer').prop('disabled', false); }
            });
        });

    }(jQuery));
});
</script>


@endsection