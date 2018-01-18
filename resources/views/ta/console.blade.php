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
                <li><a href="#usersModule" data-toggle="pill"><i class="fa fa-users fa-fw"></i> Users</a></li>
                <li><a href="#checkinsModule" data-toggle="pill"><i class="fa fa-list-ol fa-fw"></i> Check Ins</a></li>
                <li><a href="#secretWordModule" data-toggle="pill"><i class="fa fa-key fa-fw"></i> Secret Word</a></li>
                <li><a href="#announcementsModule" data-toggle="pill"><i class="fa fa-bullhorn fa-fw"></i> Announcements</a></li>
                <li><a href="#exportDataModule" data-toggle="pill"><i class="fa fa-download fa-fw"></i> Export Data</a></li>
                @if (Auth::user()->is_gsi())
                <li><a href="#sectionsModule" data-toggle="pill"><i class="fa fa-calendar fa-fw"></i> Sections</a></li>
                <li><a href="#statsModule" data-toggle="pill"><i class="fa fa-bar-chart fa-fw"></i> Statistics</a></li>
                <li><a href="#eventTypesModule" data-toggle="pill"><i class="fa fa-tags fa-fw"></i> Event Types</a></li>
                <li><a href="#auditLogModule" data-toggle="pill"><i class="fa fa-history fa-fw"></i> Audit Log</a></li>
                <li><a href="#settingsModule" data-toggle="pill"><i class="fa fa-cogs fa-fw"></i> Settings</a></li>
                @endif
            </ul>
        </div>
        <div class="tab-content">
            <div id="usersModule" class="col-lg-10 tab-pane fade in" data-target="{{ route("tamoduleusers") }}">
                @include('ta.core.loading')
            </div>
            <div id="checkinsModule" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulecheckins") }}">
                @include('ta.core.loading')
            </div>
            <div id="secretWordModule" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulesecretword") }}">
                @include('ta.core.loading')
            </div>
            <div id="announcementsModule" class="col-lg-10 tab-pane fade" data-target="{{ route("tamoduleannouncements") }}">
                @include('ta.core.loading')
            </div>
            <div id="exportDataModule" class="col-lg-10 tab-pane fade" data-target="{{ route("tamoduleexport") }}">
                @include('ta.core.loading')
            </div>
            <div id="sectionsModule" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulesections") }}">
                @include('ta.core.loading')
            </div>
            <div id="statsModule" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulestats") }}">
                @include('ta.core.loading')
            </div>
            @if (Auth::user()->is_gsi())
            <div id="eventTypesModule" class="col-lg-10 tab-pane fade" data-target="{{ route("tamoduleeventtypes") }}">
                @include('ta.core.loading')
            </div>
            <div id="auditLogModule" class="col-lg-10 tab-pane fade" data-target="{{ route("tamoduleauditlog") }}">
                @include('ta.core.loading')
            </div>
            <div id="settingsModule" class="col-lg-10 tab-pane fade" data-target="{{ route("tamodulesettings") }}">
                @include('ta.core.loading')
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
        history.pushState(null, '', tab.attr('id').slice(0, -6));
    }


    function init_tabs() {
        var activeTab = $('.tab-pane#{{{ $module }}}Module');
        show_tab(activeTab);
        activeTab.addClass('in active');
        $('.nav-pills a[href=#{{{ $module }}}Module]').parents('li').addClass('active');
        $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            tab = $('#' + $(e.target).attr('href').substr(1));
            show_tab(tab);
        });
    }

    $('#core-console-loader').hide();
    $('.console-container').fadeIn();
    init_tabs()

@endsection
@include('core.footer')
