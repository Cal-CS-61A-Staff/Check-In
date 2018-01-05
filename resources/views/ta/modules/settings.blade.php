<div class="panel panel-default">
    <div class="panel-heading">
        <h5><i class="fa fa-cogs fa-fw"></i> Settings</h5>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route("tasavesettings") }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="form-group">
                        <label>Allow Section Signups:</label>
                        <input type="checkbox" name="inputAllowSectionSignups" value="1" @if ($allowSectionSignups == 1) checked="checked" @endif />
                    </div>
                    <div class="form-group">
                        <label>Information Content:</label>
                        <textarea name="inputInformationContent" id="informationContentTextArea" rows="10">{!! $informationContent !!}</textarea>
                    </div>
                    <input type="submit" value="Save" class="btn btn-success" />
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    CKEDITOR.replace('informationContentTextArea');
</script>