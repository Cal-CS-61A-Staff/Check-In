@include('core.header')

@if (!empty($errors))
<div class="row">
    <div class="col-lg-12">
        @foreach($errors->all() as $error)
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
        <form class="form" data-toggle="validator" id="registrationForm" method="POST" action="{{ URL::route("doregistration") }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="form-group">
                <label for="inputName">Name: </label>
                <div class="help-block with-errors"></div>
                <input class="form-control" type="text" id="inputName" name="inputName" value="{{{ Input::old('inputName') }}}" placeholder="Ex: Colin Schoen" required />
            </div>
            <div class="form-group">
                <label for="inputSID">Student ID (SID): </label>
                <div class="help-block with-errors"></div>
                <input class="form-control" type="number" id="inputSID" name="inputSID" value="{{{ Input::old('inputSID') }}}" placeholder="Ex: 12345678 (Check your Cal 1 Card)" required />
            </div>
            <div class="form-group">
                <label for="inputEmail">Email (@berkeley.edu): </label>
                <div class="help-block with-errors"></div>
                <input class="form-control" type="email" id="inputEmail" name="inputEmail" value="{{{ Input::old('inputEmail') }}}" placeholder="Ex: cschoen@berkeley.edu" required />
            </div>
            <div class="form-group">
                <label for="inputPassword">Password: </label>
                <div class="help-block with-errors"></div>
                <input class="form-control" data-minlength="8" type="password" id="inputPassword" name="inputPassword" placeholder="●●●●●●●●" required />
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" value="Register" />
                <input type="reset" class="btn btn-default" value="Reset" />
            </div>
        </form>
    </div>
</div>

@include('core.footer')
