<div class="panel panel-default">
    <div class="panel-heading">
        <h5><i class="fa fa-history fa-fw"></i> Audit Log</h5>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table id="auditLogTable" class="table table-hover table-striped">
                <thead><tr><th>Name</th><th>Type</th><th>IP</th><th>Logged At</th></thead>
                <tbody>
                @foreach ($audits as $audit)
                    <tr>
                        <td>{{{ $audit->user->name }}}</td>
                        <td>{{{ $audit->action }}}</td>
                        <td>{{{ $audit->ip }}}</td>
                        <td>{{{ $audit->created_at }}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $('#auditLogTable').DataTable();
</script>