@include('core.header')

<div class="boxLoading" style="display: none;"></div>
<div id="step1" style="margin-top: 30px;">
    <div class="row">
        <div class="col-lg-12">
            <h3><span class="label label-info">Step 1:</span> Where are you? <small>(Room #)</small></h3>
            <hr />
            <input id="inputLocation" type="text" placeholder="" class="form-control" />
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <button id="btnToStep2" class="btn btn-primary">Next <i class="fa fa-arrow-right fa-fw"></i></button>
        </div>
    </div>
</div>
<div id="step2" style="display: none;">
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <h3><span class="label label-info">Step 2:</span> What is the unique secret word? <small>(Ask your GSI)</small></h3>
            <hr />
            <input id="inputPassword" class="form-control" type="password" />
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <button id="btnToStep3" class="btn btn-primary">Next <i class="fa fa-arrow-right fa-fw"></i></button>
        </div>
    </div>
</div>
<div id="step3" style="display: none;">
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <h3><span class="label label-info">Step 3:</span> What is your Student ID? <small>(Check your CAL ID)</small></h3>
            <hr />
            <input id="inputSID" class="form-control" type="text" />
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-12">
            <button id="btnToStep4" class="btn btn-success">Complete Check In <i class="fa fa-check fa-fw"></i></button>
        </div>
    </div>
</div>
<div id="step4" style="display: none;">
    <div class="row" style="margin-top: 20px;">
        <h3>Lab assistant check in complete.</h3>
        <hr />
    </div>
</div>
@section('js')
   $("#inputLocation").typed({
       strings: ["Ex: 411 Soda Hall", "Ex: Garbarini Lounge", "Ex: HP Auditorium"],
       typeSpeed: 0
   })
   $('#btnToStep2').on('click', function() {
       $('.boxLoading').fadeIn();
       $('#step1').addClass('animated fadeOutLeft').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
            $('.boxLoading').fadeOut();
            $('#step1').hide().removeClass('animated fadeOutLeft');
            $('#step2').show().addClass('animated fadeInRight').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', $(this).removeClass('animated fadeInRight'));
       });
   });
   $('#btnToStep3').on('click', function() {
       $('.boxLoading').fadeIn();
       $('#step2').addClass('animated fadeOutLeft').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
           $('.boxLoading').fadeOut();
           $('#step2').hide().removeClass('animated fadeOutLeft');
           $('#step3').show().addClass('animated fadeInRight').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', $(this).removeClass('animated fadeInRight'));
       });
   });
   $('#btnToStep4').on('click', function() {
       $('.boxLoading').fadeIn();
       $('#step3').addClass('animated fadeOutLeft').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
           $('.boxLoading').fadeOut();
           $('#step3').hide().removeClass('animated fadeOutLeft');
           $('#step4').show().addClass('animated slideInUp').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', $(this).removeClass('animated slideInUp'));
       });
   });
   $('#btnToStep1').on('click', function() {
       $('.boxLoading').fadeIn();
       $('#step4').addClass('animated fadeOutLeft').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
           $('.boxLoading').fadeOut();
           $('#step4').hide().removeClass('animated fadeOutLeft');
           $('#step1').show().addClass('animated fadeInRight').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', $(this).removeClass('animated fadeInRight'));
       });
   });
@endsection
@include('core.footer')
