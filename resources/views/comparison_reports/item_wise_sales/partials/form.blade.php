 <form action="{{ url('comparison-reports/item-wise-sales') }}" method="GET"
    class="row g-3 align-items-end">

    <!-- From Date -->
    <div class="col-md-2">
        <label class="form-label" for="dateRangeSelector">Date Range</label>
        <select id="dateRangeSelector" name="dateRangeSelector" class="form-select">
            <option value="">Select Date Range</option>
            <option value="Today" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'Today' ? 'selected' : '' }}>Today</option>
            <option value="Yesterday" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'Yesterday' ? 'selected' : '' }}>Yesterday</option>
            <option value="This Week" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'This Week' ? 'selected' : '' }}>This Week</option>
            <option value="This Month" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'This Month' ? 'selected' : '' }}>This Month</option>
            <option value="This Quarter" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'This Quarter' ? 'selected' : '' }}>This Quarter</option>
            <option value="This Year" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'This Year' ? 'selected' : '' }}>This Year</option>
            <option value="Year to Date" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'Year to Date' ? 'selected' : '' }}>Year to Date</option>
            <option value="Previous Week" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'Previous Week' ? 'selected' : '' }}>Previous Week</option>
            <option value="Previous Month" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'Previous Month' ? 'selected' : '' }}>Previous Month</option>
            <option value="Previous Quarter" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'Previous Quarter' ? 'selected' : '' }}>Previous Quarter</option>
            <option value="Previous Year" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'Previous Year' ? 'selected' : '' }}>Previous Year</option>
            <option value="Custom Range" {{ old('dateRangeSelector', $dateRangeSelector ?? '') == 'Custom Range' ? 'selected' : '' }}>Custom Range</option>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="fromDate">From Date</label>
        <input type="date"
            name="fromDate"
            id="fromDate"
            value="{{ $fromDate }}"
            class="form-control">
    </div>

    <!-- To Date -->
    <div class="col-md-2">
        <label class="form-label" for="toDate">To Date</label>
        <input type="date"
            name="toDate"
            id="toDate"
            value="{{ $toDate }}"
            class="form-control">
    </div>

    <!-- Compare Type -->
    <div class="col-md-2">
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
    <div class="col-md-2">
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
    <div class="col-md-2 text-end">
        <button type="submit" class="btn btn-primary btn-block">
            Submit
        </button>
    </div>

</form>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>


<script>

        $(document).ready(function () {
            // Handle the date range selection
            $('#dateRangeSelector').on('change', function () {
                let range = $(this).val();
                let fromDate = null;
                let toDate = null;

                switch (range) {
                    case "Today":
                        fromDate = moment().format("YYYY-MM-DD");
                        toDate = moment().format("YYYY-MM-DD");
                        break;
                    case "Yesterday":
                        fromDate = moment().subtract(1, "days").format("YYYY-MM-DD");
                        toDate = fromDate;
                        break;
                    case "This Week":
                        fromDate = moment().startOf("week").format("YYYY-MM-DD");
                        toDate = moment().endOf("week").format("YYYY-MM-DD");
                        break;
                    case "This Month":
                        fromDate = moment().startOf("month").format("YYYY-MM-DD");
                        toDate = moment().endOf("month").format("YYYY-MM-DD");
                        break;
                    case "This Quarter":
                        fromDate = moment().startOf("quarter").format("YYYY-MM-DD");
                        toDate = moment().endOf("quarter").format("YYYY-MM-DD");
                        break;
                    case "This Year":
                        fromDate = moment().startOf("year").format("YYYY-MM-DD");
                        toDate = moment().endOf("year").format("YYYY-MM-DD");
                        break;
                    case "Year to Date":
                        fromDate = moment().startOf("year").format("YYYY-MM-DD");
                        toDate = moment().format("YYYY-MM-DD");
                        break;
                    case "Previous Week":
                        fromDate = moment().subtract(1, "week").startOf("week").format("YYYY-MM-DD");
                        toDate = moment().subtract(1, "week").endOf("week").format("YYYY-MM-DD");
                        break;
                    case "Previous Month":
                        fromDate = moment().subtract(1, "month").startOf("month").format("YYYY-MM-DD");
                        toDate = moment().subtract(1, "month").endOf("month").format("YYYY-MM-DD");
                        break;
                    case "Previous Quarter":
                        fromDate = moment().subtract(1, "quarter").startOf("quarter").format("YYYY-MM-DD");
                        toDate = moment().subtract(1, "quarter").endOf("quarter").format("YYYY-MM-DD");
                        break;
                    case "Previous Year":
                        fromDate = moment().subtract(1, "year").startOf("year").format("YYYY-MM-DD");
                        toDate = moment().subtract(1, "year").endOf("year").format("YYYY-MM-DD");
                        break;
                    case "Custom Range":
                        fromDate = ""; // Let user manually set dates
                        toDate = "";
                        break;
                }

                // Populate the date fields
                $('#fromDate').val(fromDate);
                $('#toDate').val(toDate);
            });
        });
    </script>