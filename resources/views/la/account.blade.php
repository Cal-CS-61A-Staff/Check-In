@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-edit fa-fw"></i> Account Information</h3>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    @if (!empty($errors))
    <div class="col-lg-12">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{{ $error }}}
            </div>
        @endforeach
    @endif
        <form class="form" method="POST" action="{{{ route("laaccount") }}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="form-group">
                <label for="inputSID">Student ID (SID):</label>
                <input type="number" class="form-control" id="inputSID" name="inputSID" @if (old("inputSID") != "") value="{{{ old("inputSID") }}}" @else value="{{{ $user->sid }}}" @endif"/>
            </div>
            <div class="form-group">
                <label for="inputName">Name: </label>
                <input type="text" class="form-control" id="inputName" name="inputName" @if (old("inputName") != "") value="{{{ old("inputName") }}}" @else value="{{{ $user->name }}}" @endif"/>
            </div>
            <div class="form-group">
                <label for="inputEmail">Email: </label>
                <input type="email" class="form-control" id="inputEmail" name="inputEmail" @if (old("inputEmail") != "") value="{{{ old("inputEmail") }}}" @else value="{{{ $user->email }}}" @endif/>
            </div>
            <div class="form-group">
                <label for="inputPassword">Password (leave blank if not changing): </label>
                <input type="password" class="form-control" id="inputPassword" name="inputPassword" />
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" value="Edit Account" />
                <input type="reset" class="btn btn-default" value="Reset" />
            </div>
        </form>
    </div>
</div>

@include('core.footer')
