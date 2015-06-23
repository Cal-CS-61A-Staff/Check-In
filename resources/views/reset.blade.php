@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-unlock-alt fa-fw"></i> Forgot Password</h3>
        <hr />
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <form class="form" action="{{ route("doreset") }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="form-group">
                <label for="inputEmail">Email: </label>
                <input type="text" class="form-control" name="inputEmail" id="inputEmail" />
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" value="Send Reset Email" />
            </div>
        </form>
    </div>
</div>

@include('core.footer')
