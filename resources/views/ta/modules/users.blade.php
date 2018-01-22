<div id="usersModule" class="panel panel-default">
    <div id="userSubModule" style="display: none;"></div>
    <div id="usersSubModule">
        <div class="panel-heading">
            <h5><i class="fa fa-users fa-fw"></i> Users</h5>
        </div>
        <div class="panel-body">
            <div class="well">
                <label>Hours Filtering: </label><br />
                <input type="text" id="min" placeholder="Minimum total hours" />
                <input type="text" id="max" placeholder="Maximum total hours" />
            </div>
            <div class="table-responsive">
                <table id="userTable" class="table table-hover table-striped">
                    <thead>
                    <tr><th>Name</th><th>Email</th><th># of Hours</th><th># of Check Ins</th><th>Created At</th><th>Actions</th></tr>
                    </thead>
                    <tfoot>
                    <tr><th>Name</th><th>Email</th><th># of Hours</th><th># of Check Ins</th><th>Created At</th><th>Actions</th></tr>
                    </tfoot>
                    @foreach ($users as $user)
                        <tr data-target="{{{ route("tamoduleuser", $user->id) }}}">
                            <td>{{{ $user->name }}} @if ($user->is_gsi()) <strong>(GSI)</strong> @elseif ($user->is_tutor()) <strong>(Tutor)</strong> @endif</td>
                            <td>{{{ $user->email }}}</td>
                            <td><span class="badge">{{{ ($user_hours[$user->id]) }}}</span></td>
                            <td>{{{ count($user->checkins) }}}</td>
                            <td>{{{ $user->created_at }}}</td>
                            <td>
                                <span class="userActionsSpan"><span id="actions">
                                    <button data-toggle="tooltip" data-placement="top" title="Add internal only feedback" data-uid="{{{ $user->id }}}" class="btn btn-info addLAFeedbackBtn">
                                                        <i class="fa fa-comment fa-fw"></i>
                                    </button>
                                    <button data-toggle="tooltip" data-placement="top" title="Check In User" data-uid="{{{ $user->id }}}" data-name="{{{ $user->name }}}" class="btn btn-info checkInUserBtn">
                                        <i class="fa fa-check-circle-o fa-fw"></i>
                                    </button>
                                    </span>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
<div id="checkInUserModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Check In <strong><span id="checkInUserName"></span></strong></h4>
            </div>
            <form class="form" method="POST" action="{{ route("tacheckinuser") }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="inputUID" name="inputUID" value="" />
                <div class="modal-body">
                        <div class="form-group">
                            <label for="inputLocation">Type</label>
                            <select class="form-control" name="inputLocation" id="inputLocation">
                                @foreach ($types as $type)
                                    <option value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputDate">Section Date</label>
                            <input type="text" readonly name="inputDate" id="inputDate" placeholder="Date" />
                        </div>
                        <div class="form-group">
                            <label for="inputTime">Section <strong>Start</strong> Time</label>
                            <input type="text" readonly name="inputTime" id="inputTime" placeholder="Start Time" />
                        </div>
                        <div class="form-group">
                            <label for="inputGSI">GSI: </label>
                            <select class="form-control" name="inputGSI">
                                @foreach ($staff as $staffer)
                                    <option value="{{{ $staffer->id }}}">{{{ $staffer->name }}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="inputMakeup">Makeup: </label>
                            <select class="form-control" name="inputMakeup">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-info" value="Check In" />
                </div>
            </form>
        </div>
    </div>
</div>
<div id="checkInUserModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Check In <strong><span id="checkInUserName"></span></strong></h4>
            </div>
            <form id="checkInModalForm" class="form" method="POST" action="{{ route("tacheckinuser") }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="inputUID" name="inputUID" value="" />
                <input type="hidden" id="checkInInputID" name="inputID" value="" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="checkInInputLocation">Type</label>
                        <select class="form-control" name="inputLocation" id="checkInInputLocation">
                            @foreach ($types as $type)
                                <option value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="checkInInputDate">Section Date</label>
                        <input class="inputDate" type="text" readonly name="inputDate" id="checkInInputDate" placeholder="Date" />
                    </div>
                    <div class="form-group">
                        <label for="checkInInputTime">Section <strong>Start</strong> Time</label>
                        <input class="inputTime" type="text" readonly name="inputTime" id="checkInInputTime" placeholder="Start Time" />
                    </div>
                    <div class="form-group">
                        <label for="checkInInputGSI">GSI: </label>
                        <select class="form-control" id="checkInInputGSI" name="inputGSI">
                            @foreach ($staff as $staffer)
                                <option value="{{{ $staffer->id }}}">{{{ $staffer->name }}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="checkInInputMakeup">Makeup: </label>
                        <select class="form-control" id="checkInInputMakeup" name="inputMakeup">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-info" id="checkInSubmitBtn" value="Check In" />
                </div>
            </form>
        </div>
    </div>
</div>
<div id="addLAFeedbackModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Add Internal Only Feedback</h4>
            </div>
            <form action="{{ route("tafeedbackadd") }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="addFeedbackInputLA" name="inputLA" value="" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="inputFeedback">Feedback:</label>
                        <textarea rows="8" name="inputFeedback" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-success" value="Save Feedback" />
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('.addLAFeedbackBtn').on('click', function() {
        var uid = $(this).attr("data-uid");
        $('#addFeedbackInputLA').val(uid);
        $('#addLAFeedbackModal').modal('show');
    });
    $('.checkInUserBtn').on('click', function() {
        $('#checkInModalForm').attr('action', '{{{ route("tacheckinuser") }}}');
        $('#checkInUserName').html($(this).attr("data-name"));
        $('#inputUID').val($(this).attr("data-uid"));
        $('#checkInInputID').val("");
        $('#checkInInputLocation')[0].selectedIndex = 0
        $('#checkInInputDate').val($(this).attr("data-date"));
        $('#checkInInputTime').val($(this).attr("data-time"));
        $('#checkInInputGSI')[0].selectedIndex = 0
        $('#checkInInputMakeup')[0].selectedIndex = 0
        $('#checkInSubmitBtn').val("Check In");
        $('#checkInUserModal').modal('show');
    });
    $('#inputDate, .inputDate').pickadate();
    $('#inputTime, .inputTime').pickatime();

    $('#userTable tfoot th').each( function () {
        var title = $('#userTable thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

    var table2 = $('#userTable').DataTable();

    // Apply the search
    table2.columns().every( function () {
        var that = this;

        $( 'input', this.footer() ).on( 'keyup change', function () {
            that
                .search( this.value )
                .draw();
        } );
    } );

    $('#min, #max').on("keyup", function() {
        table2.draw();
    });
    $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var min = parseInt( $('#min').val(), 10 );
            var max = parseInt( $('#max').val(), 10 );
            var checkins = parseFloat( data[2] ) || 0;

            if ( ( isNaN( min ) && isNaN( max ) ) ||
                ( isNaN( min ) && checkins <= max ) ||
                ( min <= checkins   && isNaN( max ) ) ||
                ( min <= checkins   && age <= checkins ) )
            {
                return true;
            }
            return false;
        }
    );

    table2.on('click', 'tbody tr', function() {
        var userUrl = $(table2.row(this).node()).attr("data-target");
        var userSubModule = $('#userSubModule');
        userSubModule.load(userUrl);
        $('#usersSubModule').hide();
        userSubModule.fadeIn();

    });

</script>