@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-info-circle fa-fw"></i> Information</h3>
    </div>
</div>

<div class="row" style="margin-top: 20px">
    <div class="col-lg-12">
        <div class="well">
            <div class="alert alert-info">Welcome to the {{ trans("global.class") }}Lab Assistant Management Platform.</div>
            <p>{{ trans("global.class") }} Lab Assistant Meeting Slides: <a href="{{ url("meeting/slides") }}"><strong>{{ url("meeting/slides") }}</strong></a></p>
            <p>If you are a lab assistant please <a target="_blank" href="{{ route("registration") }}">register</a> an account prior to your first day assisting in
                the labs. You will be required to check in <strong>(when you are physically lab assisting with a present GSI)</strong> using this system every time you assist in the labs, office hours and other course functions
                in order to receive hours credit. Once you have registered an account you can <a target="_blank" href="{{ route("login") }}">login</a> and use the
                <a target="_blank" href="{{ route("lacheckin") }}">check in</a> form as well as view your attendance history. Please direct any questions to <a target="_blank" href="https://piazza.com/class/i99jqe1hfat3nb">Piazza</a>.</p>
            <hr />
            <iframe src="https://ghbtns.com/github-btn.html?user=colinschoen&repo=Check-In&type=fork&count=true&size=large" frameborder="0" scrolling="0" width="158px" height="30px"></iframe>
            <p><small>Developed by Colin Schoen</small></p>
        </div>
    </div>
</div>

@include('core.footer')
