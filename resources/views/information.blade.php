@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-info-circle fa-fw"></i> Information</h3>
    </div>
</div>

<div class="row" style="margin-top: 20px">
    <div class="col-lg-12">
        <div class="well">
            <div class="alert alert-info">Welcome to the {{ trans("global.class") }} Lab Assistant Management Platform.</div>
            <div>
                {!! $informationContent !!}
            </div>
            <hr />
            <iframe src="https://ghbtns.com/github-btn.html?user=colinschoen&repo=Check-In&type=fork&count=true&size=large" frameborder="0" scrolling="0" width="158px" height="30px"></iframe>
            <p><small>Developed by Colin Schoen</small></p>
        </div>
    </div>
</div>

@include('core.footer')
