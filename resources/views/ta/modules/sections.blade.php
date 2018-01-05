<div class="panel panel-default">
    <div class="panel-heading">
        <h5><i class="fa fa-calendar fa-fw"></i> Sections</h5>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <button id="createSectionBtn" class="btn btn-info">Create Section <i class="fa fa-plus fa-fw"></i></button>
                <button id="uploadSectionsCSVBtn" class="btn btn-info">Upload Sections CSV <i class="fa fa-file fa-fw"></i></button>
                <button id="viewYourLabAssistantsBtn" class="btn btn-default">View Your Lab Assistants <i class="fa fa-eye fa-fw"></i></button>
            </div>
        </div>
        <br />
        <div id="viewYourLabAssistantsDiv" class="well" style="display: none;">
            <h5>Your sections' lab assistants:</h5><br />
            Names: <input type="text" class="form-control" value="{{{ implode(",", $yourLabAssistantsNames) }}}" disabled="disabled"/>
            Emails: <input type="text" class="form-control" value="{{{ implode(", ", $yourLabAssistantsEmails) }}}" disabled="disabled"/>
        </div>
        <div id="uploadSectionsCSV" style="display: none;" class="row">
            <div class="col-lg-12">
                <div class="well">
                    The CSV needs to have the following structure with the headers being present:
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Type</th>
                                <th>Location</th>
                                <th>GSI Email</th>
                                <th>Second GSI Email</th>
                                <th>Max Lab Assistants</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Monday</th>
                                <th>Tuesday</th>
                                <th>Wednesday</th>
                                <th>Thursday</th>
                                <th>Friday</th>
                                <th>Saturday</th>
                                <th>Sunday</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    Notes:
                    <ul>
                        <li><strong>Type</strong> values need to be spelled exactly as they were entered here on the site</li>
                        <li><strong>GSI Email</strong> user must already exist with email specified</li>
                        <li><strong>Second GSI Email</strong> optional field if left blank</li>
                        <li><strong>Max Lab Assistants</strong> set as -1 for unlimited</li>
                        <li><strong>Monday - Saturday</strong> set as 1 if section occurs on that day and 0 if not</li>
                    </ul>
                    <form method="POST" enctype="multipart/form-data" action="{{ route("tasectionimport") }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="form-group">
                            <label>CSV File: </label>
                            <input type="file" name="inputSectionCSVFile" />
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-success" value="Import Sections" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="createSectionForm" style="margin-top: 5px; display: none;" class="row">
            <div class="col-lg-12">
                <div class="well">
                    <form method="POST" action="{{ route("tasectionnew") }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="form-group">
                            <label>Type: </label>
                            <select name="inputType" class="form-control">
                                <option value="-1">Select a type</option>
                                @foreach ($types as $type)
                                    <option value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Location <small>([Room #] [Building Name])</small>: </label>
                            <input type="text" name="inputLocation" class="form-control" placeholder="Ex: 411 Soda" />
                        </div>
                        <div class="form-group">
                            <label>GSI: </label>
                            <select name="inputGSI" class="form-control">
                                <option value="-1">Select a GSI</option>
                                @foreach ($staff as $staffer)
                                    <option value="{{{ $staffer->id }}}">{{{ $staffer->name }}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Second GSI <small>(optional)</small>: </label>
                            <select name="inputSecond_GSI" class="form-control">
                                <option value="-1">Select a GSI</option>
                                @foreach ($staff as $staffer)
                                    <option value="{{{ $staffer->id }}}">{{{ $staffer->name }}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Max Lab Assistants <small>(-1 for unlimited)</small>: </label>
                            <input type="number" class="form-control" name="inputMaxLas" placeholder="Ex: 5" />
                        </div>
                        <div class="form-group">
                            <label>Days: </label>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="inputMon" value="1" /> Monday
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="inputTue" value="1" /> Tuesday
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="inputWed" value="1" /> Wednesday
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="inputThu" value="1" /> Thursday
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="inputFri" value="1" /> Friday
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="inputSat" value="1" /> Saturday
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="inputSun" value="1" /> Sunday
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Start Time: </label>
                            <input type="text" id="newSectionFormStartTimeInput" name="inputStartTime" placeholder="Ex: 4:30PM" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>End Time: </label>
                            <input type="text" id="newSectionFormEndTimeInput" name="inputEndTime" placeholder="Ex: 6:00PM" class="form-control" />
                        </div>
                        <div class="form-actions">
                            <input type="reset" class="btn btn-default" value="Reset" />
                            <input type="submit" class="btn btn-success" value="Create Section" />
                        </div>
                    </form>
                </div>
                <hr />
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table id="sectionTable" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr><th>Type</th><th>Location</th><th>Days</th><th>Start Time</th><th>End Time</th><th>GSI</th><th>Max Lab Assistants</th><th>Assigned</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        @foreach ($sections as $s)
                            <tr data-sid="{{{ $s->id }}}" data-type="{{{ $s->type }}}" data-location="{{{ $s->location }}}" data-max_las="{{{ $s->max_las}}}" data-mon="{{{ $s->mon }}}" data-tue="{{{ $s->tue }}}" data-wed="{{{ $s->wed }}}" data-thu="{{{ $s->thu }}}" data-fri="{{{ $s->fri }}}" data-sat="{{{ $s->sat }}}" data-sun="{{{ $s->sun }}}" data-gsi="{{{ $s->gsi }}}" data-second_gsi="{{{ $s->second_gsi }}}" data-start_time="{{{ $s->start_time }}}" data-end_time="{{{ $s->end_time }}}">
                                <td>{{{ $s->category->name }}}</td>
                                <td>{{{ $s->location }}}</td>
                                <td>{{{ App\Section::daysToString($s) }}}</td>
                                <td>{{{ $s->start_time }}}</td>
                                <td>{{{ $s->end_time }}}</td>
                                @if ($s->gsi == -1)
                                    <td><span style="font-style: italic">Unassigned</span></td>
                                @else
                                    <td><span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $s->ta->name }}}</span> @if ($s->second_gsi != -1) <span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $s->ta2->name }}}</span> @endif </td>
                                @endif
                                <td>@if ($s->max_las == -1 ) &infin; @else {{{ $s->max_las }}} @endif</td>
                                <td class="sectionTableAssigned">@foreach ($s->assigned as $assigned)
                                        @if (array_key_exists($assigned->uid, $double_booked)) <span style="background-color: red;">{{{ $assigned->user->name }}}</span> @else @if (array_key_exists($assigned->uid, $over_hours) && $assigned->user->hours > 0)
                                            <span style="background-color: yellow;">{{{ $assigned->user->name }}}</span>
                                        @else {{{ $assigned->user->name }}}@endif @endif, @endforeach
                                </td>
                                <td><a class="sectionViewActionsLink" href="#">View Actions</a>
                                    <p class="sectionActions" style="display: none;">
                                        <button data-toggle="tooltip" data-placement="top" data-title="Assign Lab Assistant" class="btn btn-info sectionAddLABtn">
                                            <i class="fa fa-user fa-fw"></i>
                                        </button>
                                        <button data-toggle="tooltip" data-placement="top" data-title="Edit Section" class="btn btn-warning sectionEditBtn">
                                            <i class="fa fa-edit fa-fw"></i>
                                        </button>
                                        <button data-toggle="tooltip" data-placement="top" data-title="Delete Section" class="btn btn-danger sectionDeleteBtn">
                                            <i class="fa fa-times fa-fw"></i>
                                        </button>
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addSectionLAModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Add Lab Assistant to Section</h4>
            </div>
            <form action="{{ route("tasectionaddla") }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="addSectionLAInputSID" name="inputSection" value="" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="inputEmail">Lab Assistant Email:</label>
                        <input type="text" name="inputEmail" class="form-control" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-success" value="Add Lab Assistant" />
                </div>
            </form>
        </div>
    </div>
</div>
<div id="deleteSectionModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Delete Section</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this section? This is an <strong>irreversible action</strong> and all
                    <strong>lab assistant assignments</strong> for this section will also be <strong>permanently removed</strong>.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a id="deleteSectionFinalLink" href="#"><button class="btn btn-danger"><i class="fa fa-times fa-fw"></i> Permanently Delete Section</button></a>
            </div>
        </div>
    </div>
</div>
<div id="editSectionModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Edit Section</h4>
            </div>
            <form class="form" method="POST" action="{{ route("tasectionedit") }}">
                <input id="editSectionInputHidden" type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" id="editSectionInputSID" name="inputSID" value="" />
                <div class="modal-body">
                    <div class="form-group">
                        <label>Type: </label>
                        <select id="editSectionInputType" name="inputType" class="form-control">
                            <option value="-1">Select a type</option>
                            @foreach ($types as $type)
                                <option value="{{{ $type->id }}}">{{{ $type->name }}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Location <small>([Room #] [Building Name])</small>: </label>
                        <input type="text" id="editSectionInputLocation" name="inputLocation" class="form-control" placeholder="Ex: 411 Soda" />
                    </div>
                    <div class="form-group">
                        <label>GSI: </label>
                        <select id="editSectionInputGSI" name="inputGSI" class="form-control">
                            <option value="-1">Select a GSI</option>
                            @foreach ($staff as $staffer)
                                <option value="{{{ $staffer->id }}}">{{{ $staffer->name }}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Second GSI <small>(Optional)</small>: </label>
                        <select id="editSectionInputSecond_GSI" name=inputSecond_GSI class="form-control">
                            <option value="-1">Select a GSI</option>
                            @foreach ($staff as $staffer)
                                <option value="{{{ $staffer->id }}}">{{{ $staffer->name }}}</option>
                            @endforeach
                        </select>
                    </div>                            <div class="form-group">
                        <label>Max Lab Assistants <small>(-1 for unlimited)</small>: </label>
                        <input type="number" class="form-control" name="inputMaxLas" id="editSectionInputMaxLas" placeholder="Ex: 5" />
                    </div>
                    <div class="form-group">
                        <label>Days: </label>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="editSectionInputMon" name="inputMon" value="1" /> Monday
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="editSectionInputTue" name="inputTue" value="1" /> Tuesday
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="editSectionInputWed" name="inputWed" value="1" /> Wednesday
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="editSectionInputThu" name="inputThu" value="1" /> Thursday
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="editSectionInputFri" name="inputFri" value="1" /> Friday
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="editSectionInputSat" name="inputSat" value="1" /> Saturday
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="editSectionInputSun" name="inputSun" value="1" /> Sunday
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Start Time: </label>
                        <input type="text" id="editSectionInputStartTime" name="inputStartTime" placeholder="Ex: 4:30PM" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>End Time: </label>
                        <input type="text" id="editSectionInputEndTime" name="inputEndTime" placeholder="Ex: 6:00PM" class="form-control" />
                    </div>
                    <div class="form-actions">
                    </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-success" value="Edit Section" />
            </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
    $('#createSectionBtn').on('click', function() {
        $('#createSectionForm').slideToggle();
    });
    $('#uploadSectionsCSVBtn').on('click', function() {
        $('#uploadSectionsCSV').slideToggle();
    });
    $('#newSectionFormStartTimeInput').pickatime();
    $('#newSectionFormEndTimeInput').pickatime();
    $('.sectionViewActionsLink').on('click', function(e) {
        e.preventDefault();
        $(this).hide();
        $(this).siblings('.sectionActions').fadeIn();
    });

    $('.sectionAddLABtn').on('click', function() {
        console.log("triggered");
        var tr = $(this).closest('tr');
        $('#addSectionLAInputSID').val(tr.attr('data-sid'));
        $('#addSectionLAModal').modal('show');

    });
    $('.sectionEditBtn').on('click', function() {
        var tr = $(this).closest('tr');
        $('#editSectionInputSID').val(tr.attr('data-sid'));
        $('#editSectionInputType option[value="'+ tr.attr('data-type')  +'"]').attr("selected", true);
        $('#editSectionInputLocation').val(tr.attr('data-location'));
        $('#editSectionInputGSI option[value="'+ tr.attr('data-gsi')  +'"]').attr("selected", true);
        $('#editSectionInputSecond_GSI option[value="'+ tr.attr('data-second_gsi')  +'"]').attr("selected", true);
        $('#editSectionInputMaxLas').val(tr.attr('data-max_las'));
        var mon = tr.attr('data-mon');
        var tue = tr.attr('data-tue');
        var wed = tr.attr('data-wed');
        var thu = tr.attr('data-thu');
        var fri = tr.attr('data-fri');
        var sat = tr.attr('data-sat');
        var sun = tr.attr('data-sun');
        if (mon == 1)
            $('#editSectionInputMon').attr("checked", true);
        if (tue == 1)
            $('#editSectionInputTue').attr("checked", true);
        if (wed == 1)
            $('#editSectionInputWed').attr("checked", true);
        if (thu == 1)
            $('#editSectionInputThu').attr("checked", true);
        if (fri == 1)
            $('#editSectionInputFri').attr("checked", true);
        if (sat == 1)
            $('#editSectionInputSat').attr("checked", true);
        if (sun == 1)
            $('#editSectionInputSun').attr("checked", true);
        $('#editSectionInputStartTime').val(tr.attr('data-start_time'))
        $('#editSectionInputEndTime').val(tr.attr('data-end_time'))
        $('#editSectionModal').modal('show');
    });
    $('.sectionDeleteBtn').on('click', function() {
        $('#deleteSectionFinalLink').attr("href", "{{{ URL::to('ta/section/delete') }}}/" + $(this).closest('tr').attr('data-sid'));
        $('#deleteSectionModal').modal('show');
    });
    $('[data-toggle="tooltip"]').tooltip();
    $('.sectionTableViewRequests').on('click', function(e) {
        e.preventDefault();
        $(this).hide();
        $(this).siblings('p').fadeIn();
    });
    $('.unassignLabAssistantLink').on('click', function(e) {
        e.preventDefault();
        var sid = $(this).attr('data-sid');
        var uid = $(this).attr('data-uid');
        var name = $(this).attr('data-name');
        var l = $(this);
        var _token = "{{{ csrf_token() }}}";
        $.ajax({
            type: "POST",
            url: "{{ route("tasectionunassign") }}",
            data: {_token: _token, inputSID: sid, inputUID: uid}
        })
            .success(function(received) {
                    l.fadeOut();
                }
            );
    });
    $('#sectionTable').DataTable({
        paging: false
    });
    $('#viewYourLabAssistantsBtn').on('click', function() {
        $('#viewYourLabAssistantsDiv').slideToggle();
    });
</script>