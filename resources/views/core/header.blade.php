<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>CS61A - Check In</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <link media="all" type="text/css" rel="stylesheet" href="/css/theme.css">
    <link media="all" type="text/css" rel="stylesheet" href="/css/custom.css">
    <link media="all" type="text/css" rel="stylesheet" href="/css/animate.css">
    <link media="all" type="text/css" rel="stylesheet" href="/packages/pickadate/themes/classic.css">
    <link media="all" type="text/css" rel="stylesheet" href="/packages/pickadate/themes/classic.date.css">
    <link media="all" type="text/css" rel="stylesheet" href="/packages/pickadate/themes/classic.time.css">
    <link media="all" type="text/css" rel="stylesheet" href="https://cdn.datatables.net/1.10.8/css/dataTables.bootstrap.min.css">
    <link href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>
<nav class="navbar navbar-static-top bs-docs-nav" id="top" role="banner">
    <div style="margin-top: 5px;" class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbarCollapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span style="background-color: #999999" class="icon-bar"></span>
                <span style="background-color: #999999" class="icon-bar"></span>
                <span style="background-color: #999999" class="icon-bar"></span>
            </button>
            <a href="../" class="navbar-brand">CS61A <small>LAM 1.1</small></a>
        </div>
        <div id="navbarCollapse" class="nav collapse navbar-collapse bs-navbar-collapse">
            <ul class="nav navbar-nav">
                @if (Auth::guest())
                    <li><a href="{{ URL::route("login") }}"><i class="fa fa-sign-in fa-fw"></i> Login</a></li>
                    <li><a href="{{ URL::route("registration") }}"><i class="fa fa-user-plus fa-fw"></i> Register</a></li>
                @endif
                @if (Auth::check())
                    <li><a href="{{ URL::route("lacheckin") }}"><i class="fa fa-check-circle-o fa-fw"></i> Check In</a></li>
                    <li><a href="{{ URL::route("laqueue") }}"><i class="fa fa-hand-paper-o fa-fw"></i> Queue</a></li>
                    <li><a href="{{ URL::route('laattendance') }}"><i class="fa fa-list-ol fa-fw"></i> Attendance</a></li>
                    <li><a href="{{ URL::route('laassignments') }}"><i class="fa fa-map-signs fa-fw"></i> Assignments</a></li>
                    @if (Auth::user()->is_gsi())
                        <li><a href="{{ URL::route('taconsole') }}"><i class="fa fa-bookmark fa-fw"></i> TA Console</a></li>
                    @endif
                    @if (Auth::user()->is_tutor())
                        <li><a href="{{ URL::route('taconsole') }}"><i class="fa fa-bookmark fa-fw"></i> Tutor Console</a></li>
                    @endif
                @endif
                <li><a href="{{ URL::route("information") }}"><i class="fa fa-info-circle fa-fw"></i> Information</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::check())
                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#"><i class="fa fa-user fa-fw"></i> {{{ Auth::user()->name }}} <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{ URL::route('laaccount') }}"><i class="fa fa-edit fa-fw"></i> Edit Account</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ URL::route("logout") }}"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                    </ul>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
<header class="marquee">
    <div class="row">
        <div class="col-lg-12" style="text-align: center;">
            <h1>CS61A <small>Lab Assistant Manager</small></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2 col-sm-offset-6">
            <span class="toContent"><i class="fa fa-chevron-down fa-fw"></i></span>
        </div>
    </div>
</header>
<div class="container">
    @if (count($announcements) > 0)
        <div class="row">
            <div class="col-lg-12">
                @foreach ($announcements as $announcement)
                    <div class="alert alert-warning"><i class="fa fa-bullhorn fa-fw"></i> <strong>{{{ $announcement->header }}}</strong> - {{{ $announcement->body }}}</div>
                @endforeach
            </div>
        </div>
    @endif
    @if (Session::has("message"))
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-warning alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {{{ Session::get("message") }}}
                </div>
            </div>
        </div>
    @endif
    @if (count($errors) > 0)
        <div class="row">
            <div class="col-lg-12">
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">
                        {{ $error }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif
