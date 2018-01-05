@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-bookmark fa-fw"></i> @if (Auth::user()->is_gsi()) TA @else Tutor @endif Console</h3>
    </div>
</div>
@include('ta.core.loading', ['id' => 'core-console-loader'])
<div class="console-container">
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-2">
            <ul class="nav nav-pills nav-stacked">
                <li class="active"><a href="#laSearchPanel" data-toggle="pill"><i class="fa fa-users fa-fw"></i> Users</a></li>
                <li><a href="#laCheckInsPanel" data-toggle="pill"><i class="fa fa-list-ol fa-fw"></i> Check Ins</a></li>
                <li><a href="#secretWordPanel" data-toggle="pill"><i class="fa fa-key fa-fw"></i> Secret Word</a></li>
                <li><a href="#announcementsPanel" data-toggle="pill"><i class="fa fa-bullhorn fa-fw"></i> Announcements</a></li>
                <li><a href="#exportDataPanel" data-toggle="pill"><i class="fa fa-download fa-fw"></i> Export Data</a></li>
                @if (Auth::user()->is_gsi())
                <li><a href="#sectionPanel" data-toggle="pill"><i class="fa fa-calendar fa-fw"></i> Sections</a></li>
                <li><a href="#statsPanel" data-toggle="pill"><i class="fa fa-bar-chart fa-fw"></i> Statistics</a></li>
                <li><a href="#eventTypesPanel" data-toggle="pill"><i class="fa fa-tags fa-fw"></i> Event Types</a></li>
                <li><a href="#auditLogPanel" data-toggle="pill"><i class="fa fa-history fa-fw"></i> Audit Log</a></li>
                <li><a href="#settingsPanel" data-toggle="pill"><i class="fa fa-cogs fa-fw"></i> Settings</a></li>
                @endif
            </ul>
        </div>
        <div class="tab-content">
            <div id="laSearchPanel" class="col-lg-10 tab-pane fade in active" data-target="{{ route("tamoduleusers") }}">
                @include('ta.core.loading')
            </div>
            <div id="laCheckInsPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulecheckins") }}">
                @include('ta.core.loading')
            </div>
            <div id="secretWordPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulesecretword") }}">
                @include('ta.core.loading')
            </div>
            <div id="announcementsPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamoduleannouncements") }}">
                @include('ta.core.loading')
            </div>
            <div id="exportDataPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamoduleexport") }}">
                @include('ta.core.loading')
            </div>
            <div id="sectionPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulesections") }}">
                @include('ta.core.loading')
            </div>
            <div id="statsPanel" class="col-lg-10 tab-pane fade">
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
            </div>
            @if (Auth::user()->is_gsi())
            <div id="eventTypesPanel" class="col-lg-10 tab-pane fade">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><i class="fa fa-tags fa-fw"></i> Event Types</h5>
                    </div>
                    <div class="panel-body">
                        <button id="newEventTypeBtn" class="btn btn-info"><i class="fa fa-plus fa-fw"></i> New Event Type</button>
                        <div id="newEventTypeDiv" style="display: none;">
                            <form class="form" method="POST" action="{{ route("tanewtype") }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <div class="form-group">
                                    <label for="inputEventTypeName">Type Name: </label>
                                    <input type="text" class="form-control" name="inputName" id="inputEventTypeName" placeholder="Ex: Office Hours" />
                                </div>
                                <div class="form-group">
                                    <label for="inputEventTypeHours">Hours <small>(Should be in the form of 1.0 or 1.5, not 1:30)</small>:</label>
                                    <input type="text" class="form-control" name="inputHours" id="inputEventTypeHours" placeholder="1.5" />
                                </div>
                                <div class="form-group">
                                    <label for="inputEventTypeHidden">Hidden <small>(If hidden an event type is not selectable by Lab Assistants when checking in)</small>: </label>
                                    <input class="form-control" id="inputHidden" type="checkbox" value="1" name="inputHidden" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-success" value="Create Event Type" />
                                </div>
                            </form>
                        </div>
                        <hr />
                        <label for="existingEventTypeSelect">Modify Existing Event Types: </label>
                        <select id="existingEventTypeSelect" class="form-control">
                            <option value="-1">Select an Event Type</option>
                            @foreach ($types as $type)
                                <option data-hours="{{{ $type->hours }}}" data-hidden="{{{ $type->hidden }}}" data-name="{{{ $type->name }}}" value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                            @endforeach
                        </select>
                        <div style="display: none;" id="modifyEventTypeDiv">
                            <form class="form" method="POST" action="{{ route("taupdatetype") }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <input id="modifyEventTypeTID" type="hidden" name="inputTID" value="" />
                                <div class="form-group">
                                    <label for="inputExistingEventTypeName">Type Name:</label>
                                    <input type="text" class="form-control" name="inputName" id="inputExistingEventTypeName" placeholder="Ex: Office Hours" />
                                </div>
                                <div class="form-group">
                                    <label for="inputExistingEventTypeHours">Hours <small>(Should be in the form of 1.0 or 1.5, not 1:30)</small>:</label>
                                    <input type="text" class="form-control" name="inputHours" id="inputExistingEventTypeHours" placeholder="1.5" />
                                </div>
                                <div class="form-group">
                                    <label for="inputExistingEventTypeHidden">Hidden <small>(If hidden an event type is not selectable by Lab Assistants when checking in)</small>: </label>
                                    <input class="form-control" id="modifyEventTypeHidden" type="checkbox" value="1" name="inputHidden" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-success" value="Update Event Type" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div id="auditLogPanel" class="col-lg-10 tab-pane fade">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><i class="fa fa-history fa-fw"></i> Audit Log</h5>
                    </div>
                    <div class="panel-body">
                      <div class="table-responsive">
                            <table id="auditLogTable" class="table table-hover table-striped">
                                <thead><tr><th>Name</th><th>Type</th><th>IP</th><th>Logged At</th></thead>
                                <tbody>
                                    @foreach ($audits as $audit)
                                        <tr>
                                            <td>{{{ $audit->user->name }}}</td>
                                            <td>{{{ $audit->action }}}</td>
                                            <td>{{{ $audit->ip }}}</td>
                                            <td>{{{ $audit->created_at }}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="settingsPanel" class="col-lg-10 tab-pane fade">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><i class="fa fa-cogs fa-fw"></i> Settings</h5>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="{{ route("tasavesettings") }}" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                    <div class="form-group">
                                        <label>Allow Section Signups:</label>
                                        <input type="checkbox" name="inputAllowSectionSignups" value="1" @if ($allowSectionSignups == 1) checked="checked" @endif />
                                    </div>
                                    <div class="form-group">
                                        <label>Information Content:</label>
                                        <textarea name="inputInformationContent" id="informationContentTextArea" rows="10">{!! $informationContent !!}</textarea>
                                    </div>
                                    <input type="submit" value="Save" class="btn btn-success" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>



@section('js')
    function show_tab(tab) {
        if (tab.attr('data-loaded') != 'true') {
            tab.load(tab.attr('data-target'));
            tab.attr('loaded', 'true');
        }
    }

    function init_tabs() {
        show_tab($('.tab-pane.active'));
        $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            tab = $('#' + $(e.target).attr('href').substr(1));
            show_tab(tab);
        });
    }

    $('#core-console-loader').hide();
    $('.console-container').fadeIn();
    init_tabs()
    $('#editSectionInputStartTime').pickatime();
    $('#editSectionInputEndTime').pickatime();

    $('#auditLogTable').DataTable();
    @if (Auth::user()->is_gsi())
    CKEDITOR.replace('informationContentTextArea');
    @endif



    $('#newEventTypeBtn').on('click', function() {
        $('#newEventTypeDiv').slideToggle();
    });

    $('#existingEventTypeSelect').on('change', function () {
        if ($(this).val() == -1)
            $('#modifyEventTypeDiv').slideUp();
        else {
            var opt = $(this).find('option:selected');
            var name = opt.text();
            $('#modifyEventTypeTID').val($(this).val());
            if (opt.attr('data-hidden') == 1)
                $('#modifyEventTypeHidgden').prop("checked", true);
            else
                $('#modifyEventTypeHidden').prop("checked", false);
            $('#inputExistingEventTypeName').val(opt.attr("data-name"));
            $('#inputExistingEventTypeHours').val(opt.attr("data-hours"));
            $('#modifyEventTypeDiv').slideDown();
        }
    });





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




@endsection
@include('core.footer')
