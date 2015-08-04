@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-bookmark fa-fw"></i> @if (Auth::user()->is_gsi()) TA @else Tutor @endif Console</h3>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-lg-2">
        <ul class="nav nav-pills nav-stacked">
            <li class="active"><a href="#laSearchPanel" data-toggle="pill"><i class="fa fa-users fa-fw"></i> Users</a></li>
            <li><a href="#laCheckInsPanel" data-toggle="pill"><i class="fa fa-list-ol fa-fw"></i> Check Ins</a></li>
            <li><a href="#secretWordPanel" data-toggle="pill"><i class="fa fa-key fa-fw"></i> Secret Word</a></li>
            <li><a href="#announcementsPanel" data-toggle="pill"><i class="fa fa-bullhorn fa-fw"></i> Announcements</a></li>
            <li><a href="#exportDataPanel" data-toggle="pill"><i class="fa fa-download fa-fw"></i> Export Data</a></li>
            @if (Auth::user()->is_gsi())
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
                        <label>Check In Filtering: </label><br />
                        <input type="text" id="min" placeholder="Minimum total check ins" />
                        <input type="text" id="max" placeholder="Maximum total check ins" />
                    </div>
                    <div class="table-responsive">
                        <table id="userTable" class="table table-hover table-striped">
                            <thead>
                                <tr><th>Name</th><th>Email</th><th># of Check Ins</th><th>Created At</th><th>Actions</th></tr>
                            </thead>
                            <tfoot>
                                <tr><th>Name</th><th>Email</th><th># of Check Ins</th><th>Created At</th><th>Actions</th></tr>
                            </tfoot>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{{ $user->name }}} @if ($user->is_gsi()) <strong>(GSI)</strong> @elseif ($user->is_tutor()) <strong>(Tutor)</strong> @endif</td>
                                    <td>{{{ $user->email }}}</td>
                                    <td>{{{ count($user->checkins) }}}</td>
                                    <td>{{{ $user->created_at }}}</td>
                                    <td>@if (Auth::user()->is_gsi()) <span class="userActionsSpan"><a href="#">View Actions</a></span><span id="actions" style="display: none;">@if ($user->is_tutor()) <a href="{{ route("tauserpromote", $user->id) }}"><button class="btn btn-warning"><i class="fa fa-bookmark fa-fw"></i> Make TA</button></a>  @endif @if ($user->access == 0) <a href="{{ route("tauserpromotetutor", $user->id) }}"><button class="btn btn-warning"><i class="fa fa-bookmark fa-fw"></i> Make Tutor</button></a> <a href="{{ route("tauserpromote", $user->id) }}"><button class="btn btn-warning"><i class="fa fa-bookmark fa-fw"></i> Make TA</button></a> @else <a href="{{ route("tauserdemote", $user->id) }}"><button class="btn btn-danger"><i class="fa fa-arrow-down fa-fw"></i> Demote</button></a> @endif @endif <button data-uid="{{{ $user->id }}}" data-name="{{{ $user->name }}}" class="btn btn-info checkInUserBtn"><i class="fa fa-plus fa-fw"></i> Check In</button></span></td>
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
                            <thead><tr><th>Name</th><th>Type</th><th>Date</th><th>Start Time</th><th>GSI</th><th>Makeup</th><th>Logged at</th></tr></thead>
                            <tfoot><tr><th>Name</th><th>Type</th><th>Date</th><th>Start Time</th><th>GSI</th><th>Makeup</th><th>Logged at</th></tr></tfoot>
                            <tbody>
                                @foreach ($checkins as $checkin)
                                    <tr>
                                        <td>{{{ $checkin->user->name }}}</td>
                                        <td>{{{ $checkin->type->name }}}</td>
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
                                <input type="submit" class="btn btn-success" value="Create Event Type" />
                            </div>
                        </form>
                    </div>
                    <hr />
                    <label for="existingEventTypeSelect">Modify Existing Event Types: </label>
                    <select id="existingEventTypeSelect" class="form-control">
                        <option value="-1">Select an Event Type</option>
                        @foreach ($types as $type)
                            <option data-hidden="{{{ $type->hidden }}}" data-name="{{{ $type->name }}}" value="{{{ $type->id }}}">{{{ $type->name }}}</option>
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
@section('js')
    $('#inputDate').pickadate();
    $('#inputTime').pickatime();

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

    var table = $('#consoleCheckInTable').DataTable();
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
    var checkins = parseFloat( data[3] ) || 0; // use data for the age column

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
@endsection
@include('core.footer')
