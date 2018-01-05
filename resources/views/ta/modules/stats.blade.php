<div class="panel panel-default">
    <div class="panel-heading">
        <h5><i class="fa fa-bar-chart fa-fw"></i> Statistics</h5>
    </div>
    <div class="panel-body">
        <div id="chart-checkins-per-week" style="height:400px;"></div>
        <div id="chart-checkins-unique-per-week" style="height:400px;"></div>
        <div id="chart-checkins-per-staff" style="height: 400px;"></div>
    </div>
</div>
<script>
    $('#chart-checkins-per-week').highcharts(
        {
            chart: { type: 'line' },
            title: { text: 'Total Checkins Per Week' },
            xAxis: { categories: [
                @foreach (array_keys($checkins_per_week) as $year)
                        @foreach(array_keys($checkins_per_week[$year]) as $week)
                    '{{{ $checkins_per_week[$year][$week]["value"] }}}',
                @endforeach
                @endforeach
            ] },
            yAxis: { title: "# of Checkins", min: 0
            },
            series: [
                {
                    name: "Checkins",
                    data: [
                    @foreach ($checkins_per_week as $year)
                    @foreach($year as $week)
                    {{{ $week["data"] }}},
                @endforeach
                @endforeach
            ]
        }
    ]
    }
    );

    $('#chart-checkins-unique-per-week').highcharts(
        {
            chart: { type: 'line' },
            title: { text: 'Total Unique User Checkins Per Week' },
            xAxis: { categories: [
                @foreach (array_keys($checkins_unique_per_week) as $year)
                        @foreach(array_keys($checkins_unique_per_week[$year]) as $week)
                    '{{{ $checkins_unique_per_week[$year][$week]["value"] }}}',
                @endforeach
                @endforeach
            ] },
            yAxis: { title: "# of Unique Lab Assistant Checkins", min: 0 },
            series: [
                {
                    name: "Unique Lab Assistants Who Checked In",
                    data: [
                    @foreach ($checkins_unique_per_week as $year)
                    @foreach($year as $week)
                    {{{ $week["data"] }}},
                @endforeach
                @endforeach
            ]
        }
    ]
    }
    );


    $('#chart-checkins-per-staff').highcharts(
        {
            chart: { type: 'column' },
            title: { text: 'Lab Assistant Checkins Per Staff' },
            xAxis: { categories: [
                @foreach ($checkins_per_staff as $staff)
                    "{{{ $staff["name"] }}}",
                @endforeach
            ]},
            series: [
                {
                    name: "Checkins",
                    data: [
                    @foreach ($checkins_per_staff as $staff)
                    {{{ $staff["data"] }}},
                @endforeach
            ]

        }
    ]

    }
    )
</script>