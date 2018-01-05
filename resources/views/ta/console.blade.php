@include('core.header')

<div class="row">
    <div class="col-lg-12">
        <h3><i class="fa fa-bookmark fa-fw"></i> @if (Auth::user()->is_gsi()) TA @else Tutor @endif Console</h3>
    </div>
</div>
@include('ta.core.loading', ['id' => 'core-console-loader'])
<div class="console-container">
    <div class="row" style="margin-top: 20px;">
        <div class="col-lg-2">
            <ul class="nav nav-pills nav-stacked">
                <li class="active"><a href="#laSearchPanel" data-toggle="pill"><i class="fa fa-users fa-fw"></i> Users</a></li>
                <li><a href="#laCheckInsPanel" data-toggle="pill"><i class="fa fa-list-ol fa-fw"></i> Check Ins</a></li>
                <li><a href="#secretWordPanel" data-toggle="pill"><i class="fa fa-key fa-fw"></i> Secret Word</a></li>
                <li><a href="#announcementsPanel" data-toggle="pill"><i class="fa fa-bullhorn fa-fw"></i> Announcements</a></li>
                <li><a href="#exportDataPanel" data-toggle="pill"><i class="fa fa-download fa-fw"></i> Export Data</a></li>
                @if (Auth::user()->is_gsi())
                <li><a href="#sectionPanel" data-toggle="pill"><i class="fa fa-calendar fa-fw"></i> Sections</a></li>
                <li><a href="#statsPanel" data-toggle="pill"><i class="fa fa-bar-chart fa-fw"></i> Statistics</a></li>
                <li><a href="#eventTypesPanel" data-toggle="pill"><i class="fa fa-tags fa-fw"></i> Event Types</a></li>
                <li><a href="#auditLogPanel" data-toggle="pill"><i class="fa fa-history fa-fw"></i> Audit Log</a></li>
                <li><a href="#settingsPanel" data-toggle="pill"><i class="fa fa-cogs fa-fw"></i> Settings</a></li>
                @endif
            </ul>
        </div>
        <div class="tab-content">
            <div id="laSearchPanel" class="col-lg-10 tab-pane fade in active" data-target="{{ route("tamoduleusers") }}">
                @include('ta.core.loading')
            </div>
            <div id="laCheckInsPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulecheckins") }}">
                @include('ta.core.loading')
            </div>
            <div id="secretWordPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulesecretword") }}">
                @include('ta.core.loading')
            </div>
            <div id="announcementsPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamoduleannouncements") }}">
                @include('ta.core.loading')
            </div>
            <div id="exportDataPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamoduleexport") }}">
                @include('ta.core.loading')
            </div>
            <div id="sectionPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulesections") }}">
                @include('ta.core.loading')
            </div>
            <div id="statsPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulestats") }}">
                @include('ta.core.loading')
            </div>
            @if (Auth::user()->is_gsi())
            <div id="eventTypesPanel" class="col-lg-10 tab-pane fade" data-target="{{ route("tamoduleeventtypes") }}">
                @include('ta.core.loading')
            </div>
            <div id="auditLogPanel" class="col-lg-10 tab-pane fade">
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
            </div>
            <div id="settingsPanel" class="col-lg-10 tab-pane fade">
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
            </div>
            @endif
        </div>
    </div>



@section('js')
    function show_tab(tab) {
        if (tab.attr('data-loaded') != 'true') {
            tab.load(tab.attr('data-target'));
            tab.attr('loaded', 'true');
        }
    }

    function init_tabs() {
        show_tab($('.tab-pane.active'));
        $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            tab = $('#' + $(e.target).attr('href').substr(1));
            show_tab(tab);
        });
    }

    $('#core-console-loader').hide();
    $('.console-container').fadeIn();
    init_tabs()


    $('#auditLogTable').DataTable();
    @if (Auth::user()->is_gsi())
    CKEDITOR.replace('informationContentTextArea');
    @endif











@endsection
@include('core.footer')
