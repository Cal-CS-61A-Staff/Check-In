@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-bookmark fa-fw"></i> TA Console</h3>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-sm-2">
        <ul class="nav nav-pills nav-stacked">
            <li class="active"><a href="#laSearchPanel" data-toggle="pill"><i class="fa fa-search fa-fw"></i> User Search</a></li>
            <li><a href="#laCheckInsPanel" data-toggle="pill"><i class="fa fa-list-ol fa-fw"></i> Check Ins</a></li>
            <li><a href="#exportDataPanel" data-toggle="pill"><i class="fa fa-download fa-fw"></i> Export Data Ins</a></li>
        </ul>
    </div>
    <div class="tab-content">
        <div id="laSearchPanel" class="col-lg-10 tab-pane fade in active">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5><i class="fa fa-search fa-fw"></i> Lab Assistant Search</h5>
                </div>
                <div class="panel-body">
                    <form class="form" id="laSearchForm" method="POST" action="#">
                        <div class="form-group">
                            <label for="inputLASearch">Enter a name, SID or email: </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search fa-fw"></i></span>
                                <input type="text" class="form-control" id="inputLASearch" />
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <tr><th>SID</th><th>Name</th><th>Email</th><th>View</th></tr>
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
                                    <tr><td>{{{ $checkin->user->name }}}</td>
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
        <div id="exportDataPanel" class="col-lg-10 tab-pane fade">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5><i class="fa fa-download fa-fw"></i> Export Data</h5>
                </div>
                <div class="panel-body">
                    <label>Click on a file to download</label><br /><br />
                    <div class="form-group">
                        <a href="#"><button class="btn btn-info"><i class="fa fa-download fa-fw"></i> Check-Ins.csv</button></a>
                    </div>
                    <div class="form-group">
                        <a href="#"><button class="btn btn-info"><i class="fa fa-download fa-fw"></i> Lab-Assistant-Roster.csv</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
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
    $("#inputLASearch").typed({
        strings: ["Ex: Colin Schoen", "Ex: cschoen@berkeley.edu", "Ex: 12345678"],
        typeSpeed: 0,
        loop: true,
    });
@endsection
@include('core.footer')
