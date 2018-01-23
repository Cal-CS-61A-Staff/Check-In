<div class="panel-heading">
    <a id="userSubModuleBackBtn"><i class="fa fa-arrow-left fa-fw"></i></a>
    <h5 style="margin-top: 30px; margin-bottom: 30px;"><strong>{{{ $user->name }}}</strong></h5>
    <ul class="nav nav-pills">
        <li class="active">
            <a href="#userInfoSubModule" data-toggle="pill"><i class="fa fa-user fa-fw"></i> Info</a>
        </li>
        <li>
            <a class="userAssignmentsSubModule" href="#userAssignmentsSubModule" data-toggle="pill"><i class="fa fa-map-signs fa-fw"></i> Section Assignments</a>
        </li>
        <li class="nav-item">
            <a class="userFeedbackSubModule" href="#userFeedbackSubModule" data-toggle="pill"><i class="fa fa-comment fa-fw"></i> Staff Feedback</a>
        </li>
    </ul>
</div>
<div class="panel-body">
    <div class="well tab-content">
        <div id="userInfoSubModule" class="tab-pane fade active in">
            <form class="form" method="POST">
                <div class="form-group">
                    <label for="inputName">Name: </label>
                    <input name="inputName" type="text" class="form-control" value="{{{ $user->name }}}" />
                </div>
                <div class="form-group">
                    <label for="inputEmail">Email <small>(Changing this may leave the user unable to log in)</small>: </label>
                    <input name="inputEmail" type="text" class="form-control" value="{{{ $user->email }}}" />
                </div>
                <div class="row form-row">
                    <div class="form-group col-md-2">
                        <label for="inputUnits">Units: </label>
                        <input name="inputUnits" type="text" class="form-control" value="{{{ $user->units }}}" />
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputHours">Hours: </label>
                        <input name="inputHours" type="text" class="form-control input-sm" value="{{{ $user->hours }}}" />
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success" value="Save" />
                </div>
        </div>
        <div id="userAssignmentsSubModule" class="tab-pane fade">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Type</th><th>Location</th><th>Days</th><th>Start Time</th><th>End Time</th><th>GSI</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user->assignments as $assignment)
                        <tr>
                            <td>{{{ $assignment->sec->category->name }}}</td>
                            <td>{{{ $assignment->sec->location }}}</td>
                            <td>{{{ App\Section::daysToString($assignment->sec) }}}</td>
                            <td>{{{ $assignment->sec->start_time }}}</td>
                            <td>{{{ $assignment->sec->end_time }}}</td>
                            <td>
                                <span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $assignment->sec->ta->name }}}</span>
                                @if (!empty($assignment->sec->ta2))
                                <span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $assignment->sec->ta2->name }}}</span>
                                @endif
                            </td>
                            <td>
                                <a class="assignmentSwapBtn" data-aid="{{{ $assignment->id }}}" href="#">
                                    <button data-toggle="tooltip" data-placement="top" data-title="Swap section assignment with another lab assistant" class="btn btn-warning btn-tiny">
                                        <i class="fa fa-arrows-h fa-fw"></i>
                                    </button>
                                </a>
                                <a class="assignmentDropBtn" data-days="{{{ App\Section::daysToString($assignment->sec) }}}" data-type="{{{ $assignment->sec->category->name }}}" data-time="{{{ $assignment->sec->start_time }}}" data-aid="{{{ $assignment->id }}}" href="#">
                                    <button data-toggle="tooltip" data-placement="top" data-title="Drop lab assistant from section" class="btn btn-danger btn-tiny">
                                        <i class="fa fa-times fa-fw"></i>
                                    </button>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="userFeedbackSubModule" class="tab-pane fade">
            <span class="label label-default">Note:</span>
            <small>This feedback while not anonymous will only be accessible by course instructors and may be used in future
                hiring decisions.</small>
            <hr />
            <form class="form">
                <div class="form-group">
                    <textarea class="form-control" rows="8" placeholder="Feedback..."></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success" value="Add Feedback" />
                </div>
            </form>
        </div>
        </form>
    </div>
</div>
<div id="dropLAFromSectionModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Confirm dropping {{{ $user->name }}}</h4>
            </div>
            <form action="{{ route("tasectionunassign") }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="dropAssignmentInputAid" name="inputAID" value="" />
                <div class="modal-body">
                    Are you sure you want to drop <strong>{{{ $user->name }}}</strong> from the following section?
                    <table style="margin-top: 20px;" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Start Time</th>
                                <th>Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td id="dropLabAssistantSectionType"></td>
                                <td id="dropLabAssistantSectionTime"></td>
                                <td id="dropLabAssistantSectionDays"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-danger" value="Drop" />
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#userSubModuleBackBtn').on('click', function() {
        $('#userSubModule').hide();
        $('#usersSubModule').fadeIn();
    });

    $('[data-toggle="tooltip"]').tooltip();

    $('.assignmentDropBtn').on('click', function() {
        var aid = $(this).attr("data-aid");
        var time = $(this).attr("data-time");
        var type = $(this).attr("data-type");
        var days = $(this).attr("data-days");
        $('#dropAssignmentInputAid').val(aid);
        $('#dropLabAssistantSectionType').html(type);
        $('#dropLabAssistantSectionTime').html(time);
        $('#dropLabAssistantSectionDays').html(days);
        $('#dropLAFromSectionModal').modal('show');
    });
</script>