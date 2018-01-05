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