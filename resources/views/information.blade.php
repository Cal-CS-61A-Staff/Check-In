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
            <!-- Place this tag where you want the button to render. -->
            <a class="github-button" href="https://github.com/Cal-CS-61A-Staff/check-in/fork" data-icon="octicon-repo-forked" data-size="large" data-show-count="true" aria-label="Fork Cal-CS-61A-Staff/check-in on GitHub">Fork</a>
            <p><small><a target="_blank" href="https://github.com/Cal-CS-61A-Staff/Check-In/graphs/contributors">Code Contributors</a></small></p>
        </div>
    </div>
</div>

@include('core.footer')
