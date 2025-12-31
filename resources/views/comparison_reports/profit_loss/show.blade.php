@extends('tmp')

@section('title', "Profit Loss")


@section('content')

    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <div class="row justify-content-center">
                    <div class="col-md-10">
                        @include('comparison_reports.profit_loss.partials.form')
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">

                        <h4 class="text-start my-4">Revenue</h4>
                        @include('comparison_reports.profit_loss.partials.revenue')

                        <h4 class="text-start my-4">Expense</h4>
                        @include('comparison_reports.profit_loss.partials.expense')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

  



@endsection
