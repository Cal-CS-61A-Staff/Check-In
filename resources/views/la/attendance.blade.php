@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-list-ol fa-fw"></i> Attendance</h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
       <h4>Total Checkins: <strong>{{ count($checkins) }}</strong></h4>
       <h4>Total Hours: <strong>{{{ $total }}}</strong></h4>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered table-responsive">
                <tr><th>Type</th><th>Date</th><th>Start Time</th><th>GSI</th><th>Makeup</th><th>Logged at</th><th>Hours</th></tr>
                @foreach($checkins as $checkin)
                    <tr>
                        <td>{{{ $checkin->type->name }}}</td>
                        <td>{{{ $checkin->date }}}</td>
                        <td>{{{ $checkin->time }}}</td>
                        <td><span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $checkin->ta->name }}}</td>
                        <td>@if ($checkin->makeup == 1) Yes @else No @endif</td>
                        <td>{{{ $checkin->created_at }}}</td>
                        <td><span class="badge"><strong>{{{ $checkin->type->hours }}}</strong></span></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

@include('core.footer')
