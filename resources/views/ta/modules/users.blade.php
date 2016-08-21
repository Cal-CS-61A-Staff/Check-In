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
