 <div class="card mb-4">
     <div class="card-body p-0">
         <div class="table-responsive">
             <table class="table table-bordered mb-0 table-sm">
                 <thead>

                     <tr class="bg-dark text-white">
                        <th class="text-start" style="width: 400px">ACCOUNTS</th>
                         @foreach ($dates as $date)
                             <th style="width: 100px" class="text-center"> {{ $date['label'] }}</th>
                         @endforeach
                     </tr>

                 </thead>

                 <tbody>
                     @forelse($expense as $level2)

                         {{-- Initialize totals array --}}
                         @php
                             $totals = array_fill(0, count($dates), 0);
                         @endphp

                         <tr>
                             <th class="text-start">{{ $level2['level2Name'] }}</th>
                             @foreach ($dates as $date)
                                 <th class="text-center"></th>
                             @endforeach
                         </tr>



                         @foreach ($level2['level3'] as $level3)
                             <tr>
                                 <td class="text-start" style="padding-left: 30px;">{{ $level3['name'] }}</td>

                                 @foreach ($level3['data'] as $i => $data)
                                     @php
                                         $amount = $data['dr'] - $data['cr'];
                                         $totals[$i] += $amount;
                                     @endphp
                                     <td class="text-end ">
                                         {{ number_format($amount, 2) }}
                                     </td>
                                 @endforeach
                             </tr>
                         @endforeach

                         {{-- Total row per level2 --}}
                         <tr class="table-light fw-bold">
                             <td>{{ $level2['level2Name'] }} Total</td>
                             @foreach ($totals as $total)
                                 <td class="text-end">
                                     {{ number_format($total, 2) }}
                                 </td>
                             @endforeach
                         </tr>
                     @empty
                         <tr>
                             <td colspan="{{ count($dates) + 1 }}" class="text-center">No data available</td>
                         </tr>
                     @endforelse


                 </tbody>
             </table>
         </div>
     </div>
 </div>
