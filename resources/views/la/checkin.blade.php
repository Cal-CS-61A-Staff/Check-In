@include('core.header')
<div id="pickDateContainer"></div>
<div id="formErrors" style="display: none;"></div>

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-check-circle-o fa-fw"></i> Check In</h3>
    </div>
</div>

<div id="formCheckIn" style="padding-top: 5px;" class="well">
    <div id="step1">
        <div class="row">
            <div class="col-lg-12">
                <h3><small>Location:</small></h3>
                <select id="inputLocation" class="form-control" name="inputLocation">
                    @foreach ($types as $type)
                    <option value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div id="step2" style="margin-top: 20px;">
        <div class="row">
            <div class="col-lg-12">
                <h3><small>Date: </small></h3>
                <input type="text" class="form-control" name="inputDate" id="inputDate" placeholder="Date" readonly />
                <input style="margin-top: 5px;" type="text" class="form-control" name="inputTime" id="inputTime" placeholder="Start Time" readonly />
            </div>
        </div>
    </div>
    <div id="step3" style="margin-top: 20px;">
        <div class="row">
            <div class="col-lg-12">
                <h3><small>GSI</small></h3>
                <select class="form-control" name="inputGSI" id="inputGSI">
                    @foreach ($tas as $ta)
                        <option value="{{{ $ta->id }}}">{{{ $ta->name }}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div id="step4">
        <div class="row" style="margin-top: 20px;">
            <div class="col-lg-12">
                <h3><small>Makeup:</small>
                <select style="margin-top: 5px;" class="form-control" name="inputMakeup" id="inputMakeup">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>
    </div>
    <div id="step5">
        <div class="row">
            <div class="col-lg-12">
                <h3><small>GSI Password (Ask GSI):</small></h3>
                <input id="inputPassword" class="form-control" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" type="password" />
            </div>
        </div>
        <div class="row" style="margin-top: 20px;">
            <div class="col-lg-12">
                <button id="submitCheckInForm" data-nid="6" data-submit-form="true" class="btn btn-success">Complete Check In <i id="checkInLoader" class="fa fa-check-circle-o fa-fw"></i></button>
            </div>
        </div>
    </div>
    <div id="step6" style="display: none;">
        <div class="row" style="margin-top: 20px;">
            <h3>Lab assistant check in complete.</h3>
        </div>
    </div>
    <div class="boxLoading" style="display: none;"></div>
</div>
@section('js')

   $('#inputPassword').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == 13 && !($('#submitCheckInForm').is(':disabled'))) {
            $('#submitCheckInForm').click();
        }
   });
   $("#submitCheckInForm").on("click", function() {
        var btn = $(this);
        btn.attr("disabled", true);
        $('#formErrors').hide();
        $('#checkInLoader').addClass("fa-spin");
        var _token = "{{ csrf_token() }}";
        var location = $('#inputLocation').val();
        var date = $('#inputDate').val();
        var time = $('#inputTime').val();
        var gsi = $('#inputGSI').val();
        var makeup = $('#inputMakeup').val();
        var password = $('#inputPassword').val();
        $.ajax({
             method: "POST",
             url: "{{ URL::route("dolacheckin") }}",
             data: {
                _token: _token,
                location: location,
                date: date,
                time: time,
                gsi: gsi,
                makeup: makeup,
                password: password,
            }
        })
            .done(function(received) {
                $('#checkInLoader').removeClass("fa-spin");
                if (received != 1) {
                    $('#formErrors').html(received).show();
                    btn.attr("disabled", false);
                }
                else {
                    $('#formCheckIn').addClass('animated zoomOut');
                    swal({
                        title: "Check In Complete",
                        text: "Your lab assistant check in was recorded successfully.",
                        type: "success"
                    },
                    function(){
                        window.location.href = '{{ route('laattendance') }}';
                    });
                }
            });

   });
   $('#inputLocation').on("change", function() {
        var val = $(this).val();
        var location = $('#inputLocation option[value=' + val + ']').text();
        $('#step2Location').html(location);
   });
   $('#inputDate').pickadate({
        "container": "#pickDateContainer",
   });
   $('#inputTime').pickatime({
        "container": "#pickDateContainer",
   });
   $('.btnNextStep').on('click', function() {
        $(this).prop("disabled", true);
        $(this).siblings("button").prop("disabled", true);
        var nid = parseInt($(this).attr("data-nid"));
        var cid = nid - 1;
        var currentStep = $('#step' + cid);
        var nextStep = $('#step' + nid);
        currentStep.addClass('animated fadeOutUp').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
            currentStep.hide().removeClass('animated fadeOutUp');
            nextStep.show().addClass('animated fadeInUp').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
               $(this).removeClass('animated fadeInUp');
               $(this).find("button").prop("disabled", false);
            });
        });
   });
   $('.btnPrevStep').on('click', function() {
        $(this).prop("disabled", true);
        $(this).siblings("button").prop("disabled", true);
        $('.boxLoading').fadeIn();
        var pid = parseInt($(this).attr("data-pid"));
        var cid = pid + 1;
        var currentStep = $('#step' + cid);
        var prevStep = $('#step' + pid);
        currentStep.addClass('animated fadeOutDown').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
            $('.boxLoading').fadeOut();
            currentStep.hide().removeClass('animated fadeOutDown');
            prevStep.show().addClass('animated fadeInDown').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
                $(this).removeClass('animated fadeInDown');
                $(this).find("button").prop("disabled", false);
           });
       });
   });
@endsection
@include('core.footer')
