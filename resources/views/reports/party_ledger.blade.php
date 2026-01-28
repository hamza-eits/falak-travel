@extends('tmp')

@section('title', $pagetitle)

@section('content')

@if (session('error'))
<div class="alert alert-{{ Session::get('class') }} p-1" id="success-alert">
  {{ Session::get('error') }}
</div>
@endif

@if (count($errors) > 0)
<div>
  <div class="alert alert-danger p-1 border-3">
    <p class="font-weight-bold">There were some problems with your input.</p>
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
</div>
@endif

<div class="main-content">
  <div class="page-content">
    <div class="container-fluid">

      <!-- PAGE TITLE -->
      <div class="row">
        <div class="col-12">
          <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Party Ledger</h4>
          </div>
        </div>
      </div>

      <!-- CARD -->
      <div class="card shadow-sm">
        <div class="card-body">

          <form action="{{ URL('/PartyLedger1') }}" method="post" id="form1">
            {{ csrf_field() }}

            <!-- PARTY -->
            <div class="col-md-4">
              <label>Party Name</label>
              <select name="PartyID" id="partyID" class="select2 form-select" required>
                <option value="">Select</option>
                @foreach ($party as $value)
                  <option value="{{ $value->PartyID }}">
                    {{ $value->PartyID }}-{{ $value->PartyName }}-{{ $value->Phone }}
                  </option>
                @endforeach
              </select>
              <span id="selectError" style="color:red; display:none;">Please select a party</span>
            </div>

            <!-- CHART ACCOUNT -->
            <div class="col-md-4 mt-2">
              <label>Chart of Account</label>
              <select name="ChartOfAccountID[]" class="select2 form-select">
                @foreach ($chartofaccount as $value)
                  <option value="{{ $value->ChartOfAccountID }}">
                    {{ $value->ChartOfAccountID }}-{{ $value->ChartOfAccountName }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- VOUCHER -->
            <div class="col-md-4 mt-2">
              <label>Voucher Type</label>
              <select name="VoucherTypeID" class="select2 form-select">
                <option value="">ALL</option>
                @foreach ($voucher_type as $value)
                  <option value="{{ $value->VoucherTypeID }}">
                    {{ $value->VoucherCode }}-{{ $value->VoucherTypeName }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- DATE RANGE -->
            @include('components.start_end_date')

        </div>

        <!-- FOOTER -->
        <div class="card-footer bg-light">
          <button type="button" class="btn-disable btn btn-success" id="online">Submit</button>
          <button type="button" class="btn-disable btn btn-primary" id="pdf">PDF</button>
          <a href="{{ URL('/') }}" class="btn btn-secondary">Cancel</a>
        </div>

        </form>
      </div>

    </div>
  </div>
</div>

<!-- ================= PDF MODAL ================= -->

<div class="modal fade" id="pdfModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Party Ledger PDF</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">
        <iframe 
          id="pdfFrame"
          name="pdfFrame"
          style="width:100%; height:80vh; border:none;">
        </iframe>
      </div>

    </div>
  </div>
</div>

<!-- ================= SCRIPTS ================= -->

<script src="https://code.jquery.com/jquery-3.6.0.js"></script>

<script>
$(document).ready(function(){

    // PDF BUTTON
    $('#pdf').click(function(e){
        e.preventDefault();

        $('#form1').attr('action', '{{ URL("/PartyLedger1PDF") }}');
        $('#form1').attr('target', 'pdfFrame');
        $('#form1').submit();

        $('#pdfModal').modal('show');
    });

    // ONLINE BUTTON
    $('#online').click(function(e){
        e.preventDefault();

        $('#form1').removeAttr('target');
        $('#form1').attr('action', '{{ URL("/PartyLedger1") }}');
        $('#form1').submit();
    });

});
</script>

<script>
$(document).ready(function(){

    const $selectID = $('#partyID');
    const $buttons = $('.btn-disable');
    const $selectError = $('#selectError');

    function toggleButtons(){
        if($selectID.val() === ""){
            $buttons.prop('disabled', true);
            $selectError.show();
        }else{
            $buttons.prop('disabled', false);
            $selectError.hide();
        }
    }

    toggleButtons();

    $selectID.on('change', toggleButtons);

});
</script>

@endsection
