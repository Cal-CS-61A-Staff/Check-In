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
        $('#checkInModalForm').attr('action', '{{{ route("taeditcheckinuser") }}}');
        $('#checkInInputID').val($(this).attr("data-id"));
        $('#checkInUserName').html($(this).attr("data-name"));
        $('#checkInInputLocation').val($(this).attr("data-type"));
        $('#checkInInputDate').val($(this).attr("data-date"));
        $('#checkInInputTime').val($(this).attr("data-time"));
        $('#checkInInputGSI').val($(this).attr("data-gsi"));
        $('#checkInInputMakeup').val($(this).attr("data-makeup"));
        $('#checkInSubmitBtn').val("Edit Check In");
        $('#checkInUserModal').modal('show');
    });
    $('#consoleCheckInTable tfoot th').each( function () {
        var title = $('#consoleCheckInTable thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

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