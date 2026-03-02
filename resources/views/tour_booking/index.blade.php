@extends('template.tmp')

@section('title', $pagetitle)

@section('content')

    {{-- =========================================================
     STYLES
     ========================================================= --}}
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            background-color: #fff;
            border: 1px solid #ced4da;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: .75rem;
            color: #495057;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 34px;
            width: 34px;
            right: 3px;
        }

        .select2-container .select2-selection--single .select2-selection__arrow b {
            border-color: #adb5bd transparent transparent transparent;
            border-width: 6px 6px 0;
        }

        .select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #adb5bd transparent !important;
            border-width: 0 6px 6px !important;
        }

        .select2-container--default .select2-search--dropdown {
            background-color: #fff;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            background-color: #fff;
            color: #74788d;
            outline: 0;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #556ee6;
        }

        .select2-container--default .select2-results__option[aria-selected="true"]:hover {
            background-color: #556ee6;
            color: #fff;
        }

        .select2-results__option {
            padding: 6px 12px;
        }

        .select2-dropdown {
            border: 1px solid rgba(0, 0, 0, .15);
            background-color: #fff;
            box-shadow: 0 .75rem 1.5rem rgba(18, 38, 63, .03);
        }

        .select2-container .select2-selection--multiple {
            min-height: 38px;
            background-color: #fff;
            border: 1px solid #ced4da !important;
        }

        .select2-container .select2-selection--multiple .select2-selection__rendered {
            padding: 2px .75rem;
        }

        .select2-container .select2-selection--multiple .select2-search__field {
            border: 0;
            color: #495057;
        }

        .select2-container .select2-selection--multiple .select2-search__field::placeholder {
            color: #495057;
        }

        .select2-container .select2-selection--multiple .select2-selection__choice {
            background-color: #eff2f7;
            border: 1px solid #f6f6f6;
            border-radius: 1px;
            padding: 0 7px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #ced4da;
        }

        .select2-container--default .select2-results__group {
            font-weight: 600;
        }

        .form-control {
            border-radius: 0 !important;
        }

        .select2 {
            border-radius: 0 !important;
            width: 100% !important;
        }

        .table-responsive {
            overflow-x: visible !important;
        }

        .paid-invoice-img {
            position: absolute;
            top: 0;
            right: 23px;
            margin-bottom: 20px;
            z-index: 9999;
            float: right;
        }

        .dropdown-divider {
            height: 0;
            margin: 0;
            overflow: hidden;
            border-top: 1px solid #eff2f7;
        }

        .error-border {
            border: 2px solid red;
        }

        .error-message {
            color: red;
            display: none;
        }

        .swal2-popup {
            font-size: 0.8rem;
            font-weight: inherit;
            color: #5E5873;
        }

        @media (max-width: 767.98px) {
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            #student_table {
                min-width: 900px;
            }
        }
    </style>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Page title --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">Travel Booking</h4>
                            <a href="{{ route('tour-booking.create') }}" class="btn btn-primary w-md float-right">
                                <i class="bx bx-plus"></i> Add New
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Alerts --}}
                @if (session('error'))
                    <div class="alert alert-{{ Session::get('class') }} p-1" id="success-alert">
                        {{ Session::get('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger p-1 border-3">
                        <p class="font-weight-bold">There were some problems with your input.</p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Filter card --}}
                @php
                    $items = DB::table('item')->get();
                    $users = DB::table('user')->get();
                @endphp

                <div class="card">
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label for="party_name">Party Name</label>
                                <input type="text" id="party_name" name="party_name" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="Phone">Phone</label>
                                <input type="text" id="Phone" name="Phone" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="startdate">From</label>
                                <input type="date" id="startdate" name="start" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="enddate">To</label>
                                <input type="date" id="enddate" name="end" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="ItemID">Item</label>
                                <select name="ItemID" id="ItemID" class="form-select select2">
                                    <option value="">Select</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->ItemID }}">{{ $item->ItemName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="UserID">Salesman</label>
                                <select name="UserID" id="UserID" class="form-select select2">
                                    <option value="">Select</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->UserID }}">{{ $user->FullName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex flex-wrap gap-2 align-items-end">
                                <button type="button" class="btn btn-danger w-md" id="filter-button">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                                <button type="button" class="btn btn-primary w-md" id="reset-dates-button">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data table --}}
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table id="student_table" class="table table-striped table-sm"
                                style="width:100%; min-width:1200px;">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Item</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Inv Date</th>
                                        <th>Pax Name</th>
                                        <th>Ref #</th>
                                        <th>Quantity</th>
                                        <th>Pick Point</th>
                                        <th>Departure</th>
                                        <th>Fare</th>
                                        <th>Tax</th>
                                        <th>Service</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Voucher</th>
                                        <th>Mode</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>{{-- /container-fluid --}}
        </div>{{-- /page-content --}}


        {{-- =========================================================
         MODAL — Payment Form (Tour Booking)
         ========================================================= --}}
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title me-3" id="paymentModalLabel">Record Payment</h5>
                        <span id="invoiceType" class="badge bg-success"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Form posts to tour booking payment route --}}
                    <form method="POST" action="{{ URL('tour-booking/payment-save') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">

                            <div class="mb-3 row">
                                <label class="col-md-2 col-form-label fw-bold">Customer Name</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="text" id="customerName" name="customer_name"
                                        readonly>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-md-2 col-form-label fw-bold">Payment #</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="text" id="InvoiceMasterID"
                                        name="InvoiceMasterID" readonly>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3 row">
                                <label class="col-md-2 col-form-label fw-bold">Invoice Amount (AED)</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="text" id="Total" name="Total" readonly>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-md-2 col-form-label fw-bold">Balance (AED)</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="text" id="balance" name="balance" readonly>
                                </div>
                                <label class="col-md-2 col-form-label fw-bold">Bank Charges (if any)</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="text" id="bankCharges" name="bank_charges"
                                        value="0">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-md-2 col-form-label fw-bold">Amount Received (AED)</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="number" id="amountReceived"
                                        name="amount_received" step="0.01" required>
                                    <span id="error-message" class="error-message">
                                        Amount received is greater than the Balance amount!
                                    </span>
                                </div>
                                <label class="col-md-2 col-form-label fw-bold">Choose Account</label>
                                <div class="col-md-4">
                                    <select name="ChartOfAccountID" id="ChartOfAccountID" class="form-select"
                                        style="width:100% !important;">
                                        @foreach ($chartofaccount as $account)
                                            <option value="{{ $account->ChartOfAccountID }}">
                                                {{ $account->ChartOfAccountName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="mb-1 row">
                                    <label class="col-md-2 col-form-label">Date</label>
                                    <div class="col-md-4">
                                        <input type="date" name="Date" class="form-control"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <label class="col-md-2 col-form-label fw-bold">Payment Mode</label>
                                <div class="col-md-4">
                                    <select name="payment_mode" id="payment-mode" class="form-select">
                                        <option value="">Select</option>
                                        <option value="CASH">CASH</option>
                                        <option value="BANK">BANK</option>
                                        <option value="CARD">CARD</option>
                                    </select>
                                    <span id="PaymentModeError" style="color:red; display:none;">
                                        Please select a payment mode
                                    </span>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3 row">
                                <label class="col-md-2 col-form-label fw-bold">Deposit To</label>
                                <div class="col-md-4">
                                    <select name="deposit_to" id="deposit-to" class="form-select"></select>
                                </div>
                                <input type="hidden" id="selectedAccountName" name="selectedAccountName">

                                <label class="col-md-2 col-form-label fw-bold">Voucher #</label>
                                <div class="col-md-4">
                                    <input class="form-control" type="text" id="voucherNumber" name="voucher_number">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-md-2 col-form-label fw-bold">Notes</label>
                                <div class="col-md-10">
                                    <textarea class="form-control" id="notes" name="notes"></textarea>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label class="col-form-label fw-bold">Attachments</label>
                                    <input class="form-control" type="file" name="file[]" multiple
                                        accept=".jpg,.jpeg,.png,.pdf">
                                    <small class="text-muted">You can upload a maximum of 5 files, 5 MB each.</small>
                                </div>
                            </div>

                        </div>{{-- /modal-body --}}

                        <input type="hidden" name="partyID" id="partyID" value="">
                        <input type="hidden" name="InvoiceTypeID" id="invoiceTypeID" value="">

                        <div class="modal-footer">
                            <button type="submit" class="btn-disable btn btn-primary waves-effect waves-light"
                                id="amountForm">
                                Record Payment
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>


        {{-- =========================================================
         MODAL — PDF View
         ========================================================= --}}
        <div class="modal fade" id="pdfViewModal" tabindex="-1" aria-labelledby="pdfViewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pdfViewModalLabel">Invoice PDF</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="pdfContainer"></div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-danger" id="print" onclick="printInvoice()">Print</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        {{-- =========================================================
         MODAL — Party Ledger
         ========================================================= --}}
        <div class="modal fade" id="partyledgermodal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="modal-title">Party Ledger</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div id="ajax_ledger"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary w-md" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /main-content --}}


    {{-- =========================================================
     SCRIPTS
     ========================================================= --}}

    {{-- PDF loader --}}
    <script>
        function loadPDF(url) {
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#pdfContainer').html(response.html);
                    $('#pdfViewModal').modal('show');
                },
                error: function() {
                    alert('Failed to load PDF.');
                }
            });
        }
    </script>

    {{-- Print invoice --}}
    <script>
        function printInvoice() {
            var printContents = document.getElementById('pdfContainer').innerHTML;
            var printFrame = document.createElement('iframe');
            Object.assign(printFrame.style, {
                position: 'fixed',
                right: '0',
                bottom: '0',
                width: '0',
                height: '0',
                border: 'none'
            });
            document.body.appendChild(printFrame);
            var doc = printFrame.contentWindow || printFrame.contentDocument;
            if (doc.document) doc = doc.document;
            doc.open();
            doc.write('<html><head><title>Invoice</title><style>body{font-family:Arial,sans-serif;}</style></head><body>' +
                printContents + '</body></html>');
            doc.close();
            printFrame.onload = function() {
                printFrame.contentWindow.focus();
                printFrame.contentWindow.print();
                setTimeout(() => document.body.removeChild(printFrame), 500);
            };
        }
    </script>

    {{-- Open TOUR BOOKING payment modal --}}
    <script>
        function openTourPaymentModal(tourMasterID) {
            $.ajax({
                url: '{{ url('tour-booking/get-payment-info') }}/' + tourMasterID,
                method: 'GET',
                success: function(r) {
                    $('#customerName').val(r.PartyName);
                    $('#InvoiceMasterID').val(r.InvoiceMasterID);
                    $('#partyID').val(r.PartyID);
                    $('#Total').val(parseFloat(r.Total).toFixed(2));
                    $('#balance').val(parseFloat(r.Balance).toFixed(2));
                    $('#bankCharges').val(parseFloat(r.BankCharges).toFixed(2));
                    $('#amountReceived').val('');
                    $('#notes').val('Payment Received Against Tour Booking #' + r.InvoiceMasterID);
                    $('#invoiceTypeID').val(r.InvoiceTypeID);
                    $('.modal-title').text('Payment for Tour Booking #' + r.InvoiceMasterID);

                    var $badge = $('#invoiceType').html(r.InvoiceType).removeClass('bg-success bg-danger');
                    if (r.InvoiceTypeID == 1) $badge.addClass('bg-success');
                    else $badge.addClass('bg-danger');

                    $('#payment-mode').val('').trigger('change');
                    $('#paymentModal').modal('show');
                },
                error: function() {
                    alert('Failed to load booking info.');
                }
            });
        }

        function openLedgerModal(partyId, partyName) {
            $('#modal-title').text('PARTY LEDGER: ' + partyId + '  ' + partyName);
            $('#partyledgermodal').modal('show');
            $.ajax({
                url: '{{ url('ajax_party_ledger') }}/' + partyId,
                method: 'GET',
                success: function(response) {
                    $('#ajax_ledger').html(response);
                }
            });
        }
    </script>

    {{-- Voucher number generation --}}
    <script>
        function generateVoucherNumber(paymentMode) {
            var invoiceTypeID = parseInt($('#invoiceTypeID').val());
            var voucherMap = {
                1: {
                    CASH: {
                        code: 5,
                        type: 'CR'
                    },
                    BANK: {
                        code: 2,
                        type: 'BR'
                    },
                    CARD: {
                        code: 2,
                        type: 'BR'
                    }
                },
                2: {
                    CASH: {
                        code: 4,
                        type: 'CP'
                    },
                    BANK: {
                        code: 1,
                        type: 'BP'
                    },
                    CARD: {
                        code: 1,
                        type: 'BP'
                    }
                }
            };
            var config = (voucherMap[invoiceTypeID] || {})[paymentMode];
            if (!config) return;
            $.ajax({
                url: '{{ url('ajax_get_voucher_number') }}',
                method: 'GET',
                data: {
                    voucher_code: config.code
                },
                success: function(response) {
                    $('#voucherNumber').val(config.type + response.vhno);
                },
                error: function() {
                    console.error('Error fetching voucher number.');
                }
            });
        }
    </script>

    {{-- Delete booking --}}
    <script>
        function delete_booking(id) {
            if (!confirm('Are you sure you want to delete this booking?')) return;
            $.ajax({
                url: '{{ URL::to('/') }}/tour-booking/delete/' + id,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(res) {
                    alert(res.message ?? 'Deleted successfully');
                    $('#student_table').DataTable().draw();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        }
    </script>

    {{-- DataTable + all event handlers --}}
    <script>
        $(document).ready(function() {

            $('body').addClass('sidebar-enable vertical-collpsed');

            /* ---- DataTable ---- */
            var table = $('#student_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url('/tour-booking') }}',
                    data: function(d) {
                        d.party_name = $('#party_name').val();
                        d.Phone = $('#Phone').val();
                        d.startdate = $('#startdate').val();
                        d.enddate = $('#enddate').val();
                        d.ItemID = $('#ItemID').val();
                        d.UserID = $('#UserID').val();
                    }
                },
                columns: [{
                        data: 'invoice_no'
                    },
                    {
                        data: 'ItemName'
                    },
                    {
                        data: 'PartyName'
                    },
                    {
                        data: 'contact'
                    },
                    {
                        data: 'inv_date',
                        render: function(data, type) {
                            if (type === 'display' || type === 'filter') {
                                var d = new Date(data);
                                return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() +
                                    1)).slice(-2) + '/' + d.getFullYear();
                            }
                            return data;
                        }
                    },
                    {
                        data: 'pax_name'
                    },
                    {
                        data: 'ref_no'
                    },
                    {
                        data: 'quantity'
                    },
                    {
                        data: 'pick_point'
                    },
                    {
                        data: 'departure_date',
                        render: function(data, type) {
                            if (type === 'display' || type === 'filter') {
                                var d = new Date(data);
                                return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() +
                                    1)).slice(-2) + '/' + d.getFullYear();
                            }
                            return data;
                        }
                    },
                    {
                        data: 'fare'
                    },
                    {
                        data: 'tax_amount'
                    },
                    {
                        data: 'service'
                    },
                    {
                        data: 'total'
                    },
                    {
                        data: 'paid'
                    },
                    {
                        data: 'balance'
                    },
                    {
                        data: 'voucher'
                    },
                    {
                        data: 'Payment_mode'
                    },
                    {
                        data: 'action',
                        orderable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                orderCellsTop: false,
                fixedHeader: true,
                retrieve: true,
                paging: false
            });

            /* ---- Filter / Reset ---- */
            $('#filter-button').on('click', function() {
                table.draw();
            });

            $('#reset-dates-button').on('click', function() {
                $('#party_name, #Phone, #startdate, #enddate').val('');
                $('#UserID, #ItemID').val(null).trigger('change');
                table.search('').columns().search('').draw();
            });

            /* ---- Start-date constraint ---- */
            $('#startdate').on('change', function() {
                var start = $(this).val();
                var end = $('#enddate').val();
                if (!end || new Date(end) < new Date(start)) $('#enddate').val(start);
                $('#enddate').attr('min', start);
            });

            /* ---- Row clicks ---- */
            // Customer column → payment modal
            $('#student_table tbody').on('click', 'tr td:nth-child(3)', function() {
                var data = table.row($(this).closest('tr')).data();
                if (data) openTourPaymentModal(data.tourmaster_id);
            });

            // Phone column → party ledger
            $('#student_table tbody').on('click', 'tr td:nth-child(4)', function() {
                var data = table.row($(this).closest('tr')).data();
                if (data) openLedgerModal(data.party_id, data.PartyName);
            });

            // Three-dot menu — record payment
            $(document).on('click', '.record-payment', function(e) {
                e.preventDefault();
                openTourPaymentModal($(this).data('invoicemasterid'));
            });

            /* ---- Payment modal helpers ---- */
            $('#paymentModal').on('shown.bs.modal', function() {
                checkPaymentModeSelection();
            });

            $('#paymentModal').on('show.bs.modal', function() {
                $('#payment-mode').trigger('change');
            });

            function checkPaymentModeSelection() {
                var selected = $('#payment-mode').val();
                $('.btn-disable').prop('disabled', selected === '');
                $('#PaymentModeError').toggle(selected === '');
            }

            /* ---- Payment mode → accounts + voucher ---- */
            $('#payment-mode').on('change', function() {
                var mode = $(this).val();
                if (mode) {
                    loadAccountsByCategory(mode);
                    generateVoucherNumber(mode);
                } else {
                    $('#voucherNumber').val('');
                    $('#deposit-to').empty().append('<option value="">Select an option</option>');
                }
                checkPaymentModeSelection();
            });

            $('#deposit-to').on('change', function() {
                $('#selectedAccountName').val($(this).find('option:selected').data('account-name'));
            });

            function loadAccountsByCategory(mode) {
                $.ajax({
                    url: '{{ url('ajax_accounts_by_category') }}',
                    method: 'GET',
                    data: {
                        category: mode
                    },
                    success: function(accounts) {
                        var $select = $('#deposit-to').empty();
                        accounts.forEach(function(a) {
                            $select.append(
                                $('<option>', {
                                    value: a.ChartOfAccountID,
                                    'data-account-name': a.ChartOfAccountName,
                                    text: a.ChartOfAccountID + ' - ' + a
                                        .ChartOfAccountName
                                })
                            );
                        });
                        if (accounts.length) $('#selectedAccountName').val(accounts[0]
                            .ChartOfAccountName);
                        checkPaymentModeSelection();
                    },
                    error: function() {
                        console.error('Error fetching accounts.');
                    }
                });
            }

            /* ---- Amount received validation ---- */
            $('#amountReceived').on('blur', function() {
                validatePaymentAmount();
            });

            function validatePaymentAmount() {
                var balance = parseFloat($('#balance').val()) || 0;
                var received = parseFloat($('#amountReceived').val()) || 0;
                var isOver = received > balance;
                $('#amountReceived').toggleClass('error-border', isOver);
                $('#error-message').toggle(isOver);
                $('#amountForm').prop('disabled', isOver);
            }

        });
    </script>

    {{-- Vendor scripts --}}
    <script src="{{ URL('/') }}/assets/libs/dropzone/dropzone-min.js"></script>
    <script src="{{ URL('/') }}/assets/js/pages/form-file-upload.init.js"></script>
    <script src="{{ URL('/') }}/assets/js/app.js"></script>
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>

@endsection
