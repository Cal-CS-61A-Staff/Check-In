@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">×</button>
            Don't have an account? <a href="{{ URL::route("registration") }}">Register now</a>.
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <a href="{{ URL::route("reset") }}"><button class="btn btn-default"><i class="fa fa-unlock-alt fa-fw"></i> Forgot Password</button></a>
        <hr />
    </div>
</div>
@if (!empty($errors))
    <div class="row">
        <div class="col-lg-12">
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {{{ $error }}}
                </div>
            @endforeach
        </div>
    </div>
@endif
<div class="row">
    <div class="col-lg-12">
        <form id="loginForm" method="POST" data-toggle="validator" action="#">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="form-group">
                <label for="inputEmail">Email: </label>
                <div class="help-block with-errors"></div>
                <input type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="Ex: cschoen@berkeley.edu" required />
            </div>
            <div class="form-group">
                <label for="inputPassword">Password: </label>
                <div class="help-block with-errors"></div>
                <input type="password" class="form-control" name="inputPassword" id="inputPassword" placeholder="●●●●●●●●" required />
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" value="Log In" />
                <input type="reset" class="btn btn-default" value="Reset" />
            </div>
        </form>
    </div>
</div>

@include('core.footer')