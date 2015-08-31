@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-bookmark fa-fw"></i> @if (Auth::user()->is_gsi()) TA @else Tutor @endif Console</h3>
    </div>
</div>
<div id="loading">
    <img id="loading-image" src="/images/console-loader.gif" />
</div>
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
                @endif
            </ul>
        </div>
        <div class="tab-content">
            <div id="laSearchPanel" class="col-lg-10 tab-pane fade in active">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><i class="fa fa-users fa-fw"></i> Users</h5>
                    </div>
                    <div class="panel-body">
                        <div class="well">
                            <label>Hours Filtering: </label><br />
                            <input type="text" id="min" placeholder="Minimum total hours" />
                            <input type="text" id="max" placeholder="Maximum total hours" />
                        </div>
                        <div class="table-responsive">
                            <table id="userTable" class="table table-hover table-striped">
                                <thead>
                                    <tr><th>Name</th><th>Email</th><th># of Hours</th><th># of Check Ins</th><th>Created At</th><th>Actions</th></tr>
                                </thead>
                                <tfoot>
                                    <tr><th>Name</th><th>Email</th><th># of Hours</th><th># of Check Ins</th><th>Created At</th><th>Actions</th></tr>
                                </tfoot>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{{ $user->name }}} @if ($user->is_gsi()) <strong>(GSI)</strong> @elseif ($user->is_tutor()) <strong>(Tutor)</strong> @endif</td>
                                        <td>{{{ $user->email }}}</td>
                                        <td><span class="badge">{{{ ($user_hours[$user->id]) }}}</span></td>
                                        <td>{{{ count($user->checkins) }}}</td>
                                        <td>{{{ $user->created_at }}}</td>
                                        <td>@if (Auth::user()->is_gsi()) <span class="userActionsSpan"><a href="#">View Actions</a></span><span id="actions" style="display: none;">@if ($user->is_tutor()) <a href="{{ route("tauserpromote", $user->id) }}"><button class="btn btn-warning"><i class="fa fa-bookmark fa-fw"></i> Make TA</button></a>  @endif @if ($user->access == 0) <a href="{{ route("tauserpromotetutor", $user->id) }}"><button class="btn btn-warning"><i class="fa fa-bookmark fa-fw"></i> Make Tutor</button></a> <a href="{{ route("tauserpromote", $user->id) }}"><button class="btn btn-warning"><i class="fa fa-bookmark fa-fw"></i> Make TA</button></a> @else <a href="{{ route("tauserdemote", $user->id) }}"><button class="btn btn-danger"><i class="fa fa-arrow-down fa-fw"></i> Demote</button></a> @endif @endif <button data-toggle="tooltip" data-placement="top" title="Check In User" data-uid="{{{ $user->id }}}" data-name="{{{ $user->name }}}" class="btn btn-info checkInUserBtn"><i class="fa fa-check-circle-o fa-fw"></i></button></span></td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="laCheckInsPanel" class="col-lg-10 tab-pane fade">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><i class="fa fa-list-ol fa-fw"></i> Check Ins</h5>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="consoleCheckInTable" class="table table-hover table-striped">
                                <thead><tr><th>Name</th><th>Type</th><th>Hours</th><th>Date</th><th>Start Time</th><th>GSI</th><th>Makeup</th><th>Logged at</th></tr></thead>
                                <tfoot><tr><th>Name</th><th>Type</th><th>Hours</th><th>Date</th><th>Start Time</th><th>GSI</th><th>Makeup</th><th>Logged at</th></tr></tfoot>
                                <tbody>
                                    @foreach ($checkins as $checkin)
                                        <tr>
                                            <td>{{{ $checkin->user->name }}}</td>
                                            <td>{{{ $checkin->type->name }}}</td>
                                            <td><span class="badge">{{{ $checkin->type->hours }}}</span></td>
                                            <td>{{{ $checkin->date }}}</td>
                                            <td>{{{ $checkin->time }}}</td>
                                            <td><span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $checkin->ta->name }}}</span></td>
                                            <td>@if ($checkin->makeup == 1) Yes @else No @endif</td>
                                            <td>{{{ $checkin->created_at }}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="secretWordPanel" class="col-lg-10 tab-pane fade">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><i class="fa fa-key fa-fw"></i> Secret Word</h5>
                    </div>
                    <div class="panel-body">
                        <strong>Current Secret Word: <span class="label label-warning"><strong>{{{ $password }}}</strong></span></strong>
                        <form class="form" method="POST" action="{{ route("taupdatepassword") }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <div class="form-group">
                                <label for="inputPassword">Update Secret Word: </label>
                                <input type="password" id="inputPassword" name="inputPassword" class="form-control" />
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="Submit" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="announcementsPanel" class="col-lg-10 tab-pane fade">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><i class="fa fa-bullhorn fa-fw"></i> Announcements</h5>
                    </div>
                    <div class="panel-body">
                        <button id="announcementNewBtn" class="btn btn-info"><i class="fa fa-plus fa-fw"></i> New Announcement</button>
                        <div id="announcementNewForm" style="margin-top: 5px; display: none;">
                            <form class="form" action="{{ route("taannouncementnew") }}" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <div class="form-group">
                                    <label for="inputNewAnnouncementHeader">Header: </label>
                                    <input type="text" name="inputHeader" id="inputNewAnnouncementHeader" placeholder="Ex: Colin's office hours moved..." class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="inputNewAnnouncementBody">Body: </label>
                                    <input type="text" name="inputBody" id="inputNewAnnouncementBody" placeholder="Ex: 06/05 office hours moved to the Woz." class="form-control" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-success" value="Publish Announcement" />
                                </div>
                            </form>
                        </div>
                        <hr />
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <tr>
                                    <th>Header</th>
                                    <th>Body</th>
                                    <th>Visibility</th>
                                    <th>Author</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                                @foreach($announcements_ta as $announcement)
                                    <tr>
                                        <td>{{{ $announcement->header }}}</td>
                                        <td>{{{ $announcement->body }}}</td>
                                        <td>@if ($announcement->hidden == 0) Hidden @else Public @endif</td>
                                        <td><span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $announcement->user->name }}}</td>
                                        <td>{{{ $announcement->created_at }}}</td>
                                        <td>
                                            @if ($announcement->hidden == 0)
                                                <a href="{{ route("taannouncementvisibility", $announcement->id) }}"><button class="btn btn-warning"><i class="fa fa-eye fa-fw"></i> Publish</button></a>
                                            @else
                                                <a href="{{ route("taannouncementvisibility", $announcement->id) }}"><button class="btn btn-warning"><i class="fa fa-eye-slash fa-fw"></i> Hide</button></a>
                                            @endif
                                            <a href="{{ route("taannouncementdelete", $announcement->id) }}"><button class="btn btn-danger"><i class="fa fa-times fa-fw"></i> Delete</button></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="exportDataPanel" class="col-lg-10 tab-pane fade">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><i class="fa fa-download fa-fw"></i> Export Data</h5>
                    </div>
                    <div class="panel-body">
                        <label>Click on a file to download</label><br /><br />
                        <div class="form-group">
                            <a href="{{ route("tadownloadcheckins") }}"><button class="btn btn-info"><i class="fa fa-download fa-fw"></i> Check-Ins.csv</button></a>
                        </div>
                        <div class="form-group">
                        <a href="{{ route("tadownloadroster") }}"><button class="btn btn-info"><i class="fa fa-download fa-fw"></i> Lab-Assistant-Roster.csv</button></a> <small>(Also includes total number of checkins per lab assistant)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sectionPanel" class="col-lg-10 tab-pane fade">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5><i class="fa fa-calendar fa-fw"></i> Sections</h5>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <button id="createSectionBtn" class="btn btn-info">Create Section <i class="fa fa-plus fa-fw"></i></button>
                            </div>
                        </div>
                        <div id="createSectionForm" style="margin-top: 5px; display: none;" class="row">
                            <div class="col-lg-12">
                                <div class="well">
                                    <form method="POST" action="{{ route("tasectionnew") }}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                        <div class="form-group">
                                            <label>Type: </label>
                                            <select name="inputType" class="form-control">
                                                <option value="-1">Select a type</option>
                                                @foreach ($types as $type)
                                                    <option value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Location <small>([Room #] [Building Name])</small>: </label>
                                            <input type="text" name="inputLocation" class="form-control" placeholder="Ex: 411 Soda" />
                                        </div>
                                        <div class="form-group">
                                            <label>GSI: </label>
                                            <select name="inputGSI" class="form-control">
                                                <option value="-1">Select a GSI</option>
                                                @foreach ($gsis as $gsi)
                                                    <option value="{{{ $gsi->id }}}">{{{ $gsi->name }}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Second GSI <small>(optional)</small>: </label>
                                            <select name="inputSecond_GSI" class="form-control">
                                                <option value="-1">Select a GSI</option>
                                                @foreach ($gsis as $gsi)
                                                    <option value="{{{ $gsi->id }}}">{{{ $gsi->name }}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Max Lab Assistants <small>(-1 for unlimited)</small>: </label>
                                            <input type="number" class="form-control" name="inputMaxLas" placeholder="Ex: 5" />
                                        </div>
                                        <div class="form-group">
                                            <label>Days: </label>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="inputMon" value="1" /> Monday
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="inputTue" value="1" /> Tuesday
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="inputWed" value="1" /> Wednesday
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="inputThu" value="1" /> Thursday
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="inputFri" value="1" /> Friday
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="inputSat" value="1" /> Saturday
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="inputSun" value="1" /> Sunday
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Start Time: </label>
                                            <input type="text" id="newSectionFormStartTimeInput" name="inputStartTime" placeholder="Ex: 4:30PM" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label>End Time: </label>
                                            <input type="text" id="newSectionFormEndTimeInput" name="inputEndTime" placeholder="Ex: 6:00PM" class="form-control" />
                                        </div>
                                        <div class="form-actions">
                                            <input type="reset" class="btn btn-default" value="Reset" />
                                            <input type="submit" class="btn btn-success" value="Create Section" />
                                        </div>
                                    </form>
                                </div>
                                <hr />
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table id="sectionTable" class="table table-bordered table-hover table-striped">
                                        <thead>
                                        <tr><th>Type</th><th>Location</th><th>Days</th><th>Start Time</th><th>End Time</th><th>GSI</th><th>Max Lab Assistants</th><th>Assigned</th><th>Requested</th><th>Actions</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sections as $s)
                                                <tr data-sid="{{{ $s->id }}}" data-type="{{{ $s->type }}}" data-location="{{{ $s->location }}}" data-max_las="{{{ $s->max_las}}}" data-mon="{{{ $s->mon }}}" data-tue="{{{ $s->tue }}}" data-wed="{{{ $s->wed }}}" data-thu="{{{ $s->thu }}}" data-fri="{{{ $s->fri }}}" data-sat="{{{ $s->sat }}}" data-sun="{{{ $s->sun }}}" data-gsi="{{{ $s->gsi }}}" data-second_gsi="{{{ $s->second_gsi }}}" data-start_time="{{{ $s->start_time }}}" data-end_time="{{{ $s->end_time }}}">
                                                    <td>{{{ $s->category->name }}}</td>
                                                    <td>{{{ $s->location }}}</td>
                                                    <td>{{{ App\Section::daysToString($s) }}}</td>
                                                    <td>{{{ $s->start_time }}}</td>
                                                    <td>{{{ $s->end_time }}}</td>
                                                @if ($s->gsi == -1)
                                                    <td><span style="font-style: italic">Unassigned</span></td>
                                                @else
                                                    <td><span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $s->ta->name }}}</span> @if ($s->second_gsi != -1) <span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $s->ta2->name }}}</span> @endif </td>
                                                @endif
                                                    <td>@if ($s->max_las == -1 ) &infin; @else {{{ $s->max_las }}} @endif</td>
                                                    <td class="sectionTableAssigned">@foreach ($s->assigned as $assigned)
                                                            <a class="unassignLabAssistantLink"
                                                               data-name="{{{ $assigned->user->name }}}"
                                                               data-uid="{{{ $assigned->uid }}}"
                                                               data-sid="{{{ $assigned->section }}}"
                                                               href="#" data-toggle="tooltip" data-placement="top"
                                                               data-title="{{{ $assigned_hours[$assigned->uid]  }}}/{{{ $assigned->user->hours }}} requested hours for {{{ $assigned->user->units }}} units">
                                                                @if (array_key_exists($assigned->uid, $double_booked)) <span style="background-color: red;">{{{ $assigned->user->name }}}</span> @else @if (array_key_exists($assigned->uid, $over_hours) && $assigned->user->hours > 0)
                                                                    <span style="background-color: yellow;">{{{ $assigned->user->name }}}</span>
                                                                @else {{{ $assigned->user->name }}}@endif @endif, @endforeach
                                                            </a>
                                                    </td>
                                                    <td>
                                                        <a class="sectionTableViewRequests" href="#">View Requests</a>
                                                        <p style="display: none;">
                                                            @foreach ($s->pref as $assigned) <a class="assignLabAssistantLink" data-name="{{{ $assigned->user->name }}}" data-uid="{{{ $assigned->uid }}}" data-sid="{{{ $assigned->section }}}" href="#" data-toggle="tooltip" data-placement="top" data-title="{{{ $assigned_hours[$assigned->uid]  }}}/{{{ $assigned->user->hours }}} requested hours for {{{ $assigned->user->units }}} units">@if (array_key_exists($assigned->user->id, $under_hours)) <strong> {{{ $assigned->user->name }}}</strong> @else {{{ $assigned->user->name }}}@endif</a>, @endforeach</td>
                                                        </p>
                                                    <td><a class="sectionViewActionsLink" href="#">View Actions</a>
                                                        <p class="sectionActions" style="display: none;">
                                                            <button class="btn btn-warning sectionEditBtn">
                                                                <i class="fa fa-edit fa-fw"></i>
                                                            </button> <button class="btn btn-danger sectionDeleteBtn">
                                                                <i class="fa fa-times fa-fw"></i>
                                                            </button>
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
            @endif
        </div>
    </div>
    <div id="checkInUserModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Check In <strong><span id="checkInUserName"></span></strong></h4>
                </div>
                <form class="form" method="POST" action="{{ route("tacheckinuser") }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" id="inputUID" name="inputUID" value="" />
                    <div class="modal-body">
                            <div class="form-group">
                                <label for="inputLocation">Type</label>
                                <select class="form-control" name="inputLocation" id="inputLocation">
                                    @foreach ($types as $type)
                                        <option value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputDate">Section Date</label>
                                <input type="text" readonly name="inputDate" id="inputDate" placeholder="Date" />
                            </div>
                            <div class="form-group">
                                <label for="inputTime">Section <strong>Start</strong> Time</label>
                                <input type="text" readonly name="inputTime" id="inputTime" placeholder="Start Time" />
                            </div>
                            <div class="form-group">
                                <label for="inputGSI">GSI: </label>
                                <select class="form-control" name="inputGSI">
                                    @foreach ($gsis as $gsi)
                                        <option value="{{{ $gsi->id }}}">{{{ $gsi->name }}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="inputMakeup">Makeup: </label>
                                <select class="form-control" name="inputMakeup">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-info" value="Check In" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="deleteSectionModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Delete Section</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this section? This is an <strong>irreversible action</strong> and all
                        <strong>lab assistant assignments</strong> for this section will also be <strong>permanently removed</strong>.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a id="deleteSectionFinalLink" href="#"><button class="btn btn-danger"><i class="fa fa-times fa-fw"></i> Permanently Delete Section</button></a>
                </div>
            </div>
        </div>
    </div>
    <div id="editSectionModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Edit Section</h4>
                </div>
                <form class="form" method="POST" action="{{ route("tasectionedit") }}">
                    <input id="editSectionInputHidden" type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" id="editSectionInputSID" name="inputSID" value="" />
                    <div class="modal-body">
                            <div class="form-group">
                                <label>Type: </label>
                                <select id="editSectionInputType" name="inputType" class="form-control">
                                    <option value="-1">Select a type</option>
                                    @foreach ($types as $type)
                                        <option value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Location <small>([Room #] [Building Name])</small>: </label>
                                <input type="text" id="editSectionInputLocation" name="inputLocation" class="form-control" placeholder="Ex: 411 Soda" />
                            </div>
                            <div class="form-group">
                                <label>GSI: </label>
                                <select id="editSectionInputGSI" name="inputGSI" class="form-control">
                                    <option value="-1">Select a GSI</option>
                                    @foreach ($gsis as $gsi)
                                        <option value="{{{ $gsi->id }}}">{{{ $gsi->name }}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Second GSI <small>(Optional)</small>: </label>
                                <select id="editSectionInputSecond_GSI" name=inputSecond_GSI class="form-control">
                                    <option value="-1">Select a GSI</option>
                                    @foreach ($gsis as $gsi)
                                        <option value="{{{ $gsi->id }}}">{{{ $gsi->name }}}</option>
                                    @endforeach
                                </select>
                            </div>                            <div class="form-group">
                                <label>Max Lab Assistants <small>(-1 for unlimited)</small>: </label>
                                <input type="number" class="form-control" name="inputMaxLas" id="editSectionInputMaxLas" placeholder="Ex: 5" />
                            </div>
                            <div class="form-group">
                                <label>Days: </label>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="editSectionInputMon" name="inputMon" value="1" /> Monday
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="editSectionInputTue" name="inputTue" value="1" /> Tuesday
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="editSectionInputWed" name="inputWed" value="1" /> Wednesday
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="editSectionInputThu" name="inputThu" value="1" /> Thursday
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="editSectionInputFri" name="inputFri" value="1" /> Friday
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="editSectionInputSat" name="inputSat" value="1" /> Saturday
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="editSectionInputSun" name="inputSun" value="1" /> Sunday
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Start Time: </label>
                                <input type="text" id="editSectionInputStartTime" name="inputStartTime" placeholder="Ex: 4:30PM" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>End Time: </label>
                                <input type="text" id="editSectionInputEndTime" name="inputEndTime" placeholder="Ex: 6:00PM" class="form-control" />
                            </div>
                            <div class="form-actions">
                            </div>
                        </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-success" value="Edit Section" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@section('js')
    $('#loading').hide();
    $('.console-container').fadeIn();
    $('#inputDate').pickadate();
    $('#inputTime').pickatime();
    $('#editSectionInputStartTime').pickatime();
    $('#editSectionInputEndTime').pickadate();

    $('#auditLogTable').DataTable();

    $('#announcementNewBtn').on('click', function() {
        $('#announcementNewForm').slideToggle();
    });

    $('.userActionsSpan').on('click', function(e) {
        e.preventDefault();
        $(this).hide();
        $(this).siblings("span").fadeIn();
    });

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
                $('#modifyEventTypeHidden').prop("checked", true);
            else
                $('#modifyEventTypeHidden').prop("checked", false);
            $('#inputExistingEventTypeName').val(opt.attr("data-name"));
            $('#inputExistingEventTypeHours').val(opt.attr("data-hours"));
            $('#modifyEventTypeDiv').slideDown();
        }
    });
    $('.checkInUserBtn').on('click', function() {
        $('#checkInUserName').html($(this).attr("data-name"));
        $('#inputUID').val($(this).attr("data-uid"));
        $('#checkInUserModal').modal('show');
    });
    $('#consoleCheckInTable tfoot th').each( function () {
    var title = $('#consoleCheckInTable thead th').eq( $(this).index() ).text();
    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

    var table = $('#consoleCheckInTable').DataTable({
        aaSorting: [[6, "desc"]]
    });
    // Apply the search
    table.columns().every( function () {
    var that = this;

    $( 'input', this.footer() ).on( 'keyup change', function () {
    that
    .search( this.value )
    .draw();
    } );
    } );

    $('#userTable tfoot th').each( function () {
    var title = $('#userTable thead th').eq( $(this).index() ).text();
    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

    var table2 = $('#userTable').DataTable();

    // Apply the search
    table2.columns().every( function () {
    var that = this;

    $( 'input', this.footer() ).on( 'keyup change', function () {
    that
    .search( this.value )
    .draw();
    } );
    } );

    $('#min, #max').on("keyup", function() {
        table2.draw();
    });
    $.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
    var min = parseInt( $('#min').val(), 10 );
    var max = parseInt( $('#max').val(), 10 );
    var checkins = parseFloat( data[2] ) || 0;

    if ( ( isNaN( min ) && isNaN( max ) ) ||
    ( isNaN( min ) && checkins <= max ) ||
    ( min <= checkins   && isNaN( max ) ) ||
    ( min <= checkins   && age <= checkins ) )
    {
    return true;
    }
    return false;
    }
    );

    $("#inputLASearch").typed({
        strings: ["Ex: Colin Schoen", "Ex: cschoen@berkeley.edu", "Ex: 12345678"],
        typeSpeed: 0,
        loop: true,
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

    $('#createSectionBtn').on('click', function() {
        $('#createSectionForm').slideToggle();
    });
    $('#newSectionFormStartTimeInput').pickatime();
    $('#newSectionFormEndTimeInput').pickatime();
    $('.sectionViewActionsLink').on('click', function(e) {
        e.preventDefault();
        $(this).hide();
        $(this).siblings('.sectionActions').fadeIn();
    });

    $('.sectionEditBtn').on('click', function() {
        var tr = $(this).closest('tr');
        console.log("data-sid = " + tr.attr('data-sid'));
        $('#editSectionInputSID').val(tr.attr('data-sid'));
        $('#editSectionInputType option[value="'+ tr.attr('data-type')  +'"]').attr("selected", true);
        $('#editSectionInputLocation').val(tr.attr('data-location'));
        $('#editSectionInputGSI option[value="'+ tr.attr('data-gsi')  +'"]').attr("selected", true);
        $('#editSectionInputSecond_GSI option[value="'+ tr.attr('data-second_gsi')  +'"]').attr("selected", true);
        $('#editSectionInputMaxLas').val(tr.attr('data-max_las'));
        var mon = tr.attr('data-mon');
        var tue = tr.attr('data-tue');
        var wed = tr.attr('data-wed');
        var thu = tr.attr('data-thu');
        var fri = tr.attr('data-fri');
        var sat = tr.attr('data-sat');
        var sun = tr.attr('data-sun');
        if (mon == 1)
            $('#editSectionInputMon').attr("checked", true);
        if (tue == 1)
            $('#editSectionInputTue').attr("checked", true);
        if (wed == 1)
            $('#editSectionInputWed').attr("checked", true);
        if (thu == 1)
            $('#editSectionInputThu').attr("checked", true);
        if (fri == 1)
            $('#editSectionInputFri').attr("checked", true);
        if (sat == 1)
            $('#editSectionInputSat').attr("checked", true);
        if (sun == 1)
            $('#editSectionInputSun').attr("checked", true);
        $('#editSectionInputStartTime').val(tr.attr('data-start_time'))
        $('#editSectionInputEndTime').val(tr.attr('data-end_time'))
        $('#editSectionModal').modal('show');
    });
    $('.sectionDeleteBtn').on('click', function() {
        $('#deleteSectionFinalLink').attr("href", "{{{ URL::to('ta/section/delete') }}}/" + $(this).closest('tr').attr('data-sid'));
        $('#deleteSectionModal').modal('show');
    });
    $('[data-toggle="tooltip"]').tooltip();
    $('.sectionTableViewRequests').on('click', function(e) {
        e.preventDefault();
        $(this).hide();
        $(this).siblings('p').fadeIn();
    });
    $('.assignLabAssistantLink').on('click', function(e) {
        e.preventDefault();
        var sid = $(this).attr('data-sid');
        var uid = $(this).attr('data-uid');
        var name = $(this).attr('data-name');
        var l = $(this);
        var _token = "{{{ csrf_token() }}}";
        $.ajax({
            type: "POST",
            url: "{{ route("tasectionassign") }}",
            data: {_token: _token, inputSID: sid, inputUID: uid}
        })
            .success(function(received) {
                var assigned = l.closest('td').siblings('.sectionTableAssigned');
                assigned.html(assigned.html() + " " + name + ",");
            }
        );
    });
$('.unassignLabAssistantLink').on('click', function(e) {
        e.preventDefault();
        var sid = $(this).attr('data-sid');
        var uid = $(this).attr('data-uid');
        var name = $(this).attr('data-name');
        var l = $(this);
        var _token = "{{{ csrf_token() }}}";
        $.ajax({
            type: "POST",
            url: "{{ route("tasectionunassign") }}",
            data: {_token: _token, inputSID: sid, inputUID: uid}
        })
            .success(function(received) {
                l.fadeOut();
            }
        );
    });
@endsection
@include('core.footer')
