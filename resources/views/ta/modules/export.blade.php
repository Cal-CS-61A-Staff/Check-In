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