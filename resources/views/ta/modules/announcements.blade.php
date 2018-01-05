<div class="panel panel-default">
    <div class="panel-heading">
        <h5><i class="fa fa-bullhorn fa-fw"></i> Announcements</h5>
    </div>
    <div class="panel-body">
        <button id="announcementNewBtn" class="btn btn-info"><i class="fa fa-plus fa-fw"></i> New Announcement</button>
        <div id="announcementNewForm" style="margin-top: 5px; display: none;">
            <form class="form" action="{{ route("taannouncementnew") }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="form-group">
                    <label for="inputNewAnnouncementHeader">Header: </label>
                    <input type="text" name="inputHeader" id="inputNewAnnouncementHeader" placeholder="Ex: Colin's office hours moved..." class="form-control" />
                </div>
                <div class="form-group">
                    <label for="inputNewAnnouncementBody">Body: </label>
                    <input type="text" name="inputBody" id="inputNewAnnouncementBody" placeholder="Ex: 06/05 office hours moved to the Woz." class="form-control" />
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success" value="Publish Announcement" />
                </div>
            </form>
        </div>
        <hr />
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <tr>
                    <th>Header</th>
                    <th>Body</th>
                    <th>Visibility</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                @foreach($announcements as $announcement)
                    <tr>
                        <td>{{{ $announcement->header }}}</td>
                        <td>{{{ $announcement->body }}}</td>
                        <td>@if ($announcement->hidden == 0) Hidden @else Public @endif</td>
                        <td><span class="label label-danger"><i class="fa fa-bookmark fa-fw"></i> {{{ $announcement->user->name }}}</td>
                        <td>{{{ $announcement->created_at }}}</td>
                        <td>
                            @if ($announcement->hidden == 0)
                                <a href="{{ route("taannouncementvisibility", $announcement->id) }}"><button class="btn btn-warning"><i class="fa fa-eye fa-fw"></i> Publish</button></a>
                            @else
                                <a href="{{ route("taannouncementvisibility", $announcement->id) }}"><button class="btn btn-warning"><i class="fa fa-eye-slash fa-fw"></i> Hide</button></a>
                            @endif
                            <a href="{{ route("taannouncementdelete", $announcement->id) }}"><button class="btn btn-danger"><i class="fa fa-times fa-fw"></i> Delete</button></a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<script>
    $('#announcementNewBtn').on('click', function() {
        $('#announcementNewForm').slideToggle();
    });
</script>