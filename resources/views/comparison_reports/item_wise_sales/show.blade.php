@extends('tmp')

@section('title', 'Item Wise Sale Report')


@section('content')

    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <div class="row justify-content-center">
                    <div class="col-md-12">
                        @include('comparison_reports.item_wise_sales.partials.form')
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-12 d-flex align-items-center gap-4">
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="view_type" value="all" checked>
                            <span class="form-check-label">All</span>
                        </label>

                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="view_type" value="no_sale">
                            <span class="form-check-label">No Sale</span>
                        </label>

                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="view_type" value="invoice">
                            <span class="form-check-label">Invoice Total</span>
                        </label>

                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="view_type" value="profit">
                            <span class="form-check-label">Profit</span>
                        </label>
                    </div>
                </div>


                <div class="card mt-2">
                    <div class="card-body">
                        <table class="table table-sm table-bordered text-nowrap">
                            <thead>
                                <tr>
                                    <th rowspan="2">Item Name</th>
                                    @foreach ($dates as $date)
                                        <th colspan="3" class="text-center date-header">
                                            {{ $date['label'] }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach ($dates as $date)
                                        <th class="text-end no-of-sales">No. Sales</th>
                                        <th class="text-end total-invoice-amount">Total Inv.</th>
                                        <th class="text-end profit">Profit</th>
                                    @endforeach    
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach ($finalData as $row)
                                    <tr>
                                        <td> {{ $row['name'] }} </td>
                                         @foreach ($row['sales'] as $sale)
                                            <td class="text-end no-of-sales">{{ $sale['no_of_sales'] }}</td>
                                            <td class="text-end total-invoice-amount">{{ number_format($sale['total_invoice_amount'], 2) }}</td>
                                            <td class="text-end profit">{{ number_format($sale['profit'], 2) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {

            function updateView(type) {
                $('.no-of-sales, .total-invoice-amount, .profit').addClass('d-none');

                if (type === 'all') {
                    $('.no-of-sales, .total-invoice-amount, .profit').removeClass('d-none');
                    $('.date-header').attr('colspan', 3);
                }

                if (type === 'no_sale') {
                    $('.no-of-sales').removeClass('d-none');
                    $('.date-header').attr('colspan', 1);
                }

                if (type === 'invoice') {
                    $('.total-invoice-amount').removeClass('d-none');
                    $('.date-header').attr('colspan', 1);
                }

                if (type === 'profit') {
                    $('.profit').removeClass('d-none');
                    $('.date-header').attr('colspan', 1);
                }
            }

            $('input[name="view_type"]').on('change', function () {
                updateView($(this).val());
            });

            updateView('all');
        });
    </script>
@endsection    