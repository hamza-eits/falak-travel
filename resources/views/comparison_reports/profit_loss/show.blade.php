@extends('tmp')

@section('title', "Profit Loss")


@section('content')

    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <div class="col-12">
                
                    @include('comparison_reports.profit_loss.partials.form')

                </div>

                   
                
                <h4 class="my-4">Revenue Comparison Report</h4>
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                
                                 <tr>
                                    <th>Accounts</th>
                                    @foreach($dates as $date)
                                        <th class="text-center"> {{ $date['label'] }}</th>
                                    @endforeach
                                </tr>

                            </thead>

                            <tbody>
                                @forelse($revenue as $level2)
                                    <tr>
                                        <th>{{ $level2['level2Name'] }}</th>
                                        @foreach($dates as $date)
                                            <th class="text-center"></th>
                                        @endforeach
                                    </tr>

                                    

                                    @foreach($level2['level3'] as $level3)
                                        <tr>
                                            <td style="padding-left: 30px;">{{ $level3['name'] }}</td>

                                            @foreach($level3['data'] as $data)
                                                <td class="text-end fw-bold">
                                                    {{ number_format($data['cr'] - $data['dr'], 2) }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach

                                @endforeach

                            </tbody>
                        </table>

                      
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

  



@endsection
