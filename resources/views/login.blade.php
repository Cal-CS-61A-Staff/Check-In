@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <form id="loginForm" method="POST" action="#">
            <div class="form-group">
                <label for="inputEmail">Email: </label>
                <input type="text" class="form-control" name="inputEmail" id="inputEmail" placeholder="Ex: cschoen@berkeley.edu" />
            </div>
            <div class="form-group">
                <label for="inputPassword">Password: </label>
                <input type="text" class="form-control" name="inputPassword" id="inputPassword" placeholder="A silly password :)" />
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-success" value="Log In" />
                <input type="reset" class="btn btn-default" value="Reset" />
            </div>
        </form>
    </div>
</div>

@include('core.footer)