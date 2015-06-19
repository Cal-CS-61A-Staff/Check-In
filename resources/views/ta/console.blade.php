@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-bookmark fa-fw"></i> TA Console</h3>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-lg-2">
        <ul class="nav nav-pills nav-stacked">
            <li class="active"><a href="#laSearchPanel" data-toggle="pill"><i class="fa fa-users fa-fw"></i> Users</a></li>
            <li><a href="#laCheckInsPanel" data-toggle="pill"><i class="fa fa-list-ol fa-fw"></i> Check Ins</a></li>
            <li><a href="#secretWordPanel" data-toggle="pill"><i class="fa fa-key fa-fw"></i> Secret Word</a></li>
            <li><a href="#exportDataPanel" data-toggle="pill"><i class="fa fa-download fa-fw"></i> Export Data</a></li>
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
                                    <td>{{{ $user->name }}}</td>
                                    <td>{{{ $user->email }}}</td>
                                    <td>{{{ count($user->checkins) }}}</td>
                                    <td>{{{ $user->created_at }}}</td>
                                    <td>@if ($user->access == 0) <a href="{{ route("tauserpromote", $user->id) }}"><button class="btn btn-warning"><i class="fa fa-bookmark fa-fw"></i> Make TA</button></a> @else <a href="{{ route("tauserdemote", $user->id) }}"><button class="btn btn-danger"><i class="fa fa-arrow-down fa-fw"></i> Revoke TA</button></a> @endif <button data-uid="{{{ $user->id }}}" data-name="{{{ $user->name }}}" class="btn btn-info checkInUserBtn"><i class="fa fa-plus fa-fw"></i> Check In</button></td>
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
                    <h5><i class="fa fa-lock fa-fw"></i> Secret Word</h5>
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
    var checkins = parseFloat( data[2] ) || 0; // use data for the age column

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
