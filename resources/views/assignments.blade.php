@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-map-signs fa-fw"></i> Assignments</h3>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="col-lg-12">
        <h4>Requested Hours Per Week: <strong>{{{ Auth::user()->hours }}}</strong></h4>
        <h4>Requested Units: <strong>{{{ Auth::user()->units }}}</strong></h4>
    </div>
</div>

<div class="row" style="margin-top: 20px;">
   <div class="col-lg-12" >
       <button id="submitAvailabilityBtn" class="btn btn-info"><i class="fa fa-plus fa-fw"></i> Choose Sections</button>
   </div>
</div>
<div id="submitAvailabilityDiv" class="row" style="display: none; margin-top: 20px;">
    <div class="col-lg-12" >
        <form action="{{{ route("doassignments") }}}" method="POST">
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
            <div class="well">
                <div class="form-group">
                    <label for="inputHours">
                        Hours Per Week:
                    </label>
                    <input class="form-control" type="number" id="inputHours" name="inputHours" value="{{{ Auth::user()->hours }}}" placeholder="Ex: 3" />
                </div>
                <div class="form-group">
                    <label for="inputUnits">Units <small>(Enter 0 if you are not lab assisting for units)</small></label>
                    <input class="form-control" type="number" id="inputUnits" name="inputUnits" value="{{{ Auth::user()->units }}}" placeholder="Ex: 1" />
                </div>
                <hr />
                <p>Select the available section or sections you will attend.</p>
                <div class="table-responsive">
                   <table id="sectionPreferencesSelectionTable" class="table table-hover table-bordered table-striped">
                       <thead>
                           <tr>
                               <th>Choose</th><th>Lab Assistants</th><th>Type</th><th>Location</th><th>GSI</th><th>Days</th><th>Start Time</th><th>End Time</th>
                           </tr>
                       </thead>
                       <tbody>
                            @foreach ($sections as $section)
                                <tr>
                                    <td>
                                        <input name="inputSections[]" value="{{{ $section->id }}}" type="checkbox" @if (in_array($section->id, $assignmentSids)) checked="checked" @elseif($section->max_las != -1 && count($section->assigned) >= $section->max_las) disabled="disabled" @endif />
                                    </td>
                                    <td>
                                        <strong>{{{ count($section->assigned) }}}/@if ($section->max_las == -1)&infin;@else{{{ $section->max_las }}} @endif</strong>
                                    </td>
                                    <td>
                                        {{{ $section->category->name }}}
                                    </td>
                                    <td>
                                        {{{ $section->location }}}
                                    </td>
                                    <td>
                                        <span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $section->ta->name }}}</span>
                                        @if ($section->second_gsi != -1)
                                            <span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $section->ta2->name }}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{{ App\Section::daysToString($section) }}}
                                    </td>
                                    <td>
                                        {{{ $section->start_time }}}
                                    </td>
                                    <td>
                                        {{{ $section->end_time }}}
                                    </td>
                                </tr>
                            @endforeach
                       </tbody>
                   </table>
                </div>
                <hr />
                <input type="submit" class="btn btn-success" value="Save Sections" />
            </form>
        </div>
    </div>
</div>
<div class="row" style="margin-top: 20px;">
    <div class="col-lg-12">
        <h4>Your Assignments: </h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Location</th>
                        <th>GSI</th>
                        <th>Days</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                </thead>
                <tbody>
                @if (count($assignments) == 0)
                   <tr>
                      <td colspan="6" style="text-align: center;">No Assignments Found</td>
                   </tr>
                @else
                @foreach ($assignments as $assignment)
                    <tr>
                        <td>
                            {{{ $assignment->sec->category->name }}}
                        </td>
                        <td>
                            {{{ $assignment->sec->location }}}
                        </td>
                        <td>
                            <span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $assignment->sec->ta->name }}}</span>
                            @if ($assignment->sec->second_gsi != -1)
                                <span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $assignment->sec->ta2->name }}}</span>
                            @endif
                        </td>
                        <td>
                            {{{ App\Section::daysToString($assignment->sec) }}}
                        </td>

                        <td>
                            {{{ $assignment->sec->start_time }}}
                        </td>
                        <td>
                            {{{ $assignment->sec->end_time }}}
                        </td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@section('js')
    $('#submitAvailabilityBtn').on('click', function() {
        $('#submitAvailabilityDiv').slideToggle();
    });

    $('#sectionPreferencesSelectionTable').DataTable({
        paging: false
    });

@endsection
@include('core.footer')