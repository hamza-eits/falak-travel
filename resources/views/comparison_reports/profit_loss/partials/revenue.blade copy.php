 <div class="card mb-4">
    <div class="card-body p-0">
        <table class="table table-bordered table-striped mb-0 table-sm">
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
                        <th class="text-start">{{ $level2['level2Name'] }}</th>
                        @foreach($dates as $date)
                            <th class="text-center"></th>
                        @endforeach
                    </tr>

                    

                    @foreach($level2['level3'] as $level3)
                        <tr>
                            <td class="text-start" style="padding-left: 30px;">{{ $level3['name'] }}</td>

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