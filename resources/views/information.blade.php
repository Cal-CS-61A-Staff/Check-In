@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-info-circle fa-fw"></i> Information</h3>
    </div>
</div>

<div class="row" style="margin-top: 20px">
    <div class="col-lg-12">
        <div class="well">
            <p><div class="alert alert-info"><strong>Welcome to the new CS61A Lab Assistant Management Platform.</strong></div></p>
            <p><a href="#"><strong>CS61A Lab Assistant Syllabus</strong></a></p>
            <p>If you are a lab assistant please <a target="_blank" href="{{ route("registration") }}">register</a> an account prior to your first day assisting in
                the labs. You will be required to check in <strong>(when you are physically lab assisting with a present GSI)</strong> using this system every time you assist in the labs, office hours and other course functions
                in order to receive hours credit. Once you have registered an account you can <a target="_blank" href="{{ route("login") }}">login</a> and use the
                <a target="_blank" href="{{ route("lacheckin") }}">check in</a> form as well as view your attendance history. Please direct any questions to <a target="_blank" href="https://piazza.com/class/i9yw3s5t8k63ge">Piazza</a> and more urgent inquiries to cschoen [at] berkeley.edu</p>
            <hr />
            <p><small>Developed by Colin Schoen</small></p>
        </div>
    </div>
</div>

@include('core.footer')
