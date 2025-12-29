@extends('tmp')

@section('title', "Profit Loss")


@section('content')

    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        @if (count($errors) > 0)
                            <div>
                                <div class="alert alert-danger p-1   border-3">
                                    <p class="font-weight-bold"> There were some problems with your input.</p>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                        @endif

                        <form action="{{ URL('comparison-reports/profit-loss') }}" class="row g-3 align-items-end">

                           

                            <!-- From Date -->
                            <div class="col-auto">
                                <label class="form-label" for="fromDate">From Date</label>
                                <input type="date" name="fromDate" value="{{ old('fromDate',request()->fromDate)}}" class="form-control">
                            </div>

                            <!-- To Date -->
                            <div class="col-auto">
                                <label class="form-label" for="toDate">To Date</label>
                                <input type="date" name="toDate" value="{{ old('toDate',request()->toDate)}}" class="form-control">
                            </div>
                            
                            <div class="col-auto">
                                 <label class="form-label">Compare Based on Period/Year</label>
                                <select id="comparedType" name="comparedType" class="form-select">
                                    <option value="period">Previous Period(s)</option>
                                    <option value="year">Previous Year(s)</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">Number of Period/Year(s)</label>
                                <select id="comparedCount" name="comparedCount" class="form-select">
                                    @for ($i=1 ; $i < 11; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            

                            <!-- Submit Button -->
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>

                        </form>
                    </div>

                   
                </div>
                <div class="row">
                    <div class="card">
                        <div class="card-body">
                           <table class="table table-bordered table-hover table-sm text-wrap">
                            <thead class="table-dark">
                                <tr>
                                    {{-- <th>Expense ID</th> --}}
                                  
                            </thead>
                            <tbody>
                                {{-- @foreach($data['expenses'] as $expense) --}}
                                <tr>
                                    {{-- <td>{{ $expense->ExpenseMasterID }}</td> --}}
                                    
                                </tr>
                                {{-- @endforeach --}}
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

  



@endsection
