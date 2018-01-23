<div class="panel panel-default">
    <div class="panel-heading">
        <h5><i class="fa fa-list-ol fa-fw"></i> Check Ins</h5>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table id="consoleCheckInTable" class="table table-hover table-striped">
                <thead><tr><th>Name</th><th>Type</th><th>Hours</th><th>Date</th><th>Start Time</th><th>GSI</th><th>Makeup</th><th>Logged at</th><th>Actions</th></tr></thead>
                <tfoot><tr><th>Name</th><th>Type</th><th>Hours</th><th>Date</th><th>Start Time</th><th>GSI</th><th>Makeup</th><th>Logged at</th><th>Actions</th></tr></tfoot>
                <tbody>
                @foreach ($checkins as $checkin)
                    <tr>
                        <td>{{{ $checkin->user->name }}}</td>
                        <td>{{{ $checkin->type->name }}}</td>
                        <td><span class="badge">{{{ $checkin->type->hours }}}</span></td>
                        <td>{{{ $checkin->date }}}</td>
                        <td>{{{ $checkin->time }}}</td>
                        <td><span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $checkin->ta->name }}}</span></td>
                        <td>@if ($checkin->makeup == 1) Yes @else No @endif</td>
                        <td>{{{ $checkin->created_at }}}</td>
                        <td><span class="checkInActionsContainer" style="display: none;">
                                                        <a data-name="{{{ $checkin->user->name }}}"
                                                           data-id="{{{ $checkin->id }}}"
                                                           data-type="{{{ $checkin->type->id }}}"
                                                           data-gsi="{{{ $checkin->ta->id }}}"
                                                           data-date="{{{ $checkin->date }}}"
                                                           data-time="{{{ $checkin->time }}}"
                                                           data-makeup="{{{ $checkin->makeup }}}"
                                                           class="btn btn-info editCheckInBtn"><i class="fa fa-edit fa-fw"></i></a><a data-id="{{{ $checkin->id }}}" class="btn btn-warning removeCheckInBtn"><i class="fa fa-times fa-fw"></i></a></span><a class="checkInActionsBtn" href="#">View Actions</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="removeCheckInModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Delete Check In</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this check in? The lab assistant's <strong>hours will be decreased</strong> and this check in <strong>permanently removed</strong>. This action will be logged.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a id="removeCheckInFinalLink" href="#"><button class="btn btn-danger"><i class="fa fa-times fa-fw"></i> Permanently Remove Check In</button></a>
            </div>
        </div>
    </div>
</div>
<div id="editCheckInUserModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Check In <strong><span id="checkInUserName"></span></strong></h4>
            </div>
            <form id="editCheckInModalForm" class="form" method="POST" action="{{ route("taeditcheckinuser") }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="editCheckInInputID" name="inputID" value="" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editCheckInInputLocation">Type</label>
                        <select class="form-control" name="inputLocation" id="editCheckInInputLocation">
                            @foreach ($types as $type)
                                <option value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editCheckInInputDate">Section Date</label>
                        <input class="inputDate" type="text" readonly name="inputDate" id="editCheckInInputDate" placeholder="Date" />
                    </div>
                    <div class="form-group">
                        <label for="editCheckInInputTime">Section <strong>Start</strong> Time</label>
                        <input class="inputTime" type="text" readonly name="inputTime" id="editCheckInInputTime" placeholder="Start Time" />
                    </div>
                    <div class="form-group">
                        <label for="editCheckInInputGSI">GSI: </label>
                        <select class="form-control" id="editCheckInInputGSI" name="inputGSI">
                            @foreach ($staff as $staffer)
                                <option value="{{{ $staffer->id }}}">{{{ $staffer->name }}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editCheckInInputMakeup">Makeup: </label>
                        <select class="form-control" id="editCheckInInputMakeup" name="inputMakeup">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-info" id="editCheckInSubmitBtn" value="Edit Check In" />
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('.checkInActionsBtn').on('click', function(e) {
        e.preventDefault();
        $(this).hide();
        $(this).siblings("span").fadeIn();
    });
    $('.removeCheckInBtn').on('click', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = "{{ route("tacheckinremove", "") }}/" + id;
        $('#removeCheckInModal').modal('show');
        $('#removeCheckInFinalLink').attr("href", url);
    });
    $('.editCheckInBtn').on('click', function() {
        $('#editCheckInInputID').val($(this).attr("data-id"));
        $('#editCheckInUserName').html($(this).attr("data-name"));
        $('#editCheckInInputLocation').val($(this).attr("data-type"));
        $('#editCheckInInputDate').val($(this).attr("data-date"));
        $('#editCheckInInputTime').val($(this).attr("data-time"));
        $('#editCheckInInputGSI').val($(this).attr("data-gsi"));
        $('#editCheckInInputMakeup').val($(this).attr("data-makeup"));
        $('#editCheckInUserModal').modal('show');
    });
    $('#consoleCheckInTable tfoot th').each( function () {
        var title = $('#consoleCheckInTable thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

    $('.inputDate').pickadate();
    $('.inputTime').pickatime();

    var table = $('#consoleCheckInTable').DataTable({
        aaSorting: [[6, "desc"]]
    });
    // Apply the search
    table.columns().every( function () {
        var that = this;

        $( 'input', this.footer() ).on( 'keyup change', function () {
            that
                .search( this.value )
                .draw();
        } );
    } );

</script>