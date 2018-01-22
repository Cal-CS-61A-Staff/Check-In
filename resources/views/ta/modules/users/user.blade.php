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
<div class="panel-body">g
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
            Assignments
        </div>
        <div id="userFeedbackSubModule" class="tab-pane fade">
            User Feedback
        </div>
        </form>
    </div>
</div>
<script>
    $('#userSubModuleBackBtn').on('click', function() {
        $('#userSubModule').hide();
        $('#usersSubModule').fadeIn();
    });
</script>