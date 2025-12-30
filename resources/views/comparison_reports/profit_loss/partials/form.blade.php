 <form action="{{ url('comparison-reports/profit-loss') }}" method="GET"
    class="row g-3 align-items-end">

    <!-- From Date -->
    <div class="col-auto">
        <label class="form-label" for="fromDate">From Date</label>
        <input type="date"
            name="fromDate"
            id="fromDate"
            value="{{ $fromDate }}"
            class="form-control">
    </div>

    <!-- To Date -->
    <div class="col-auto">
        <label class="form-label" for="toDate">To Date</label>
        <input type="date"
            name="toDate"
            id="toDate"
            value="{{ $toDate }}"
            class="form-control">
    </div>

    <!-- Compare Type -->
    <div class="col-auto">
        <label class="form-label">Compare Based on</label>
        <select name="comparedType" class="form-select">
            <option value="period"
                {{ $comparedType == 'period' ? 'selected' : '' }}>
                Previous Period(s)
            </option>
            <option value="year"
                {{ $comparedType == 'year' ? 'selected' : '' }}>
                Previous Year(s)
            </option>
        </select>
    </div>

    <!-- Compare Count -->
    <div class="col-auto">
        <label class="form-label">Number of Period/Year(s)</label>
        <select name="comparedCount" class="form-select">
            @for ($i = 1; $i <= 10; $i++)
                <option value="{{ $i }}"
                    {{ $comparedCount == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
            @endfor
        </select>
    </div>

    <!-- Submit -->
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">
            Submit
        </button>
    </div>

</form>