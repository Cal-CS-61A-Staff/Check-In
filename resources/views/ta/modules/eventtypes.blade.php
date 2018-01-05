<div class="panel panel-default">
    <div class="panel-heading">
        <h5><i class="fa fa-tags fa-fw"></i> Event Types</h5>
    </div>
    <div class="panel-body">
        <button id="newEventTypeBtn" class="btn btn-info"><i class="fa fa-plus fa-fw"></i> New Event Type</button>
        <div id="newEventTypeDiv" style="display: none;">
            <form class="form" method="POST" action="{{ route("tanewtype") }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="form-group">
                    <label for="inputEventTypeName">Type Name: </label>
                    <input type="text" class="form-control" name="inputName" id="inputEventTypeName" placeholder="Ex: Office Hours" />
                </div>
                <div class="form-group">
                    <label for="inputEventTypeHours">Hours <small>(Should be in the form of 1.0 or 1.5, not 1:30)</small>:</label>
                    <input type="text" class="form-control" name="inputHours" id="inputEventTypeHours" placeholder="1.5" />
                </div>
                <div class="form-group">
                    <label for="inputEventTypeHidden">Hidden <small>(If hidden an event type is not selectable by Lab Assistants when checking in)</small>: </label>
                    <input class="form-control" id="inputHidden" type="checkbox" value="1" name="inputHidden" />
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success" value="Create Event Type" />
                </div>
            </form>
        </div>
        <hr />
        <label for="existingEventTypeSelect">Modify Existing Event Types: </label>
        <select id="existingEventTypeSelect" class="form-control">
            <option value="-1">Select an Event Type</option>
            @foreach ($types as $type)
                <option data-hours="{{{ $type->hours }}}" data-hidden="{{{ $type->hidden }}}" data-name="{{{ $type->name }}}" value="{{{ $type->id }}}">{{{ $type->name }}}</option>
            @endforeach
        </select>
        <div style="display: none;" id="modifyEventTypeDiv">
            <form class="form" method="POST" action="{{ route("taupdatetype") }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input id="modifyEventTypeTID" type="hidden" name="inputTID" value="" />
                <div class="form-group">
                    <label for="inputExistingEventTypeName">Type Name:</label>
                    <input type="text" class="form-control" name="inputName" id="inputExistingEventTypeName" placeholder="Ex: Office Hours" />
                </div>
                <div class="form-group">
                    <label for="inputExistingEventTypeHours">Hours <small>(Should be in the form of 1.0 or 1.5, not 1:30)</small>:</label>
                    <input type="text" class="form-control" name="inputHours" id="inputExistingEventTypeHours" placeholder="1.5" />
                </div>
                <div class="form-group">
                    <label for="inputExistingEventTypeHidden">Hidden <small>(If hidden an event type is not selectable by Lab Assistants when checking in)</small>: </label>
                    <input class="form-control" id="modifyEventTypeHidden" type="checkbox" value="1" name="inputHidden" />
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success" value="Update Event Type" />
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#newEventTypeBtn').on('click', function() {
        $('#newEventTypeDiv').slideToggle();
    });

    $('#existingEventTypeSelect').on('change', function () {
        if ($(this).val() == -1)
            $('#modifyEventTypeDiv').slideUp();
        else {
            var opt = $(this).find('option:selected');
            var name = opt.text();
            $('#modifyEventTypeTID').val($(this).val());
            if (opt.attr('data-hidden') == 1)
                $('#modifyEventTypeHidgden').prop("checked", true);
            else
                $('#modifyEventTypeHidden').prop("checked", false);
            $('#inputExistingEventTypeName').val(opt.attr("data-name"));
            $('#inputExistingEventTypeHours').val(opt.attr("data-hours"));
            $('#modifyEventTypeDiv').slideDown();
        }
    });
</script>