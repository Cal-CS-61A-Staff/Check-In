@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-edit fa-fw"></i> Account Information</h3>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-lg-12">
        <form class="form" method="POST" action="#">
            <div class="form-group">
                <label for="inputSID">Student ID (SID):</label>
                <input type="number" class="form-control" id="inputSID" name="inputSID" value="{{{ $user->sid }}}"/>
            </div>
            <div class="form-group">
                <label for="inputName">Name: </label>
                <input type="text" class="form-control" id="inputName" name="inputName" value="{{{ $user->name }}} "/>
            </div>
            <div class="form-group">
                <label for="inputEmail">Email: </label>
                <input type="email" class="form-control" id="inputEmail" name="inputEmail" value="{{{ $user->email }}}" />
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
