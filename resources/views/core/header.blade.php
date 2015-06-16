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
    <link href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<header class="navbar navbar-static-top bs-docs-nav" id="top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <a href="../" class="navbar-brand">CS61A <small>Lab Assistant Manager</small></a>
        </div>
        <nav class="collapse navbar-collapse bs-navbar-collapse">
            <ul class="nav navbar-nav">
                @if (Auth::guest())
                <li><a href="{{ URL::route("login") }}"><i class="fa fa-sign-in fa-fw"></i> Login</a></li>
                <li><a href="{{ URL::route("registration") }}"><i class="fa fa-user-plus fa-fw"></i> Registration</a></li>
                @endif
                <li><a href="#"><i class="fa fa-bookmark fa-fw"></i> TA Console</a></li>
                <li><a href="#"><i class="fa fa-question-circle fa-fw"></i> Help</a></li>
            </ul>
        </nav>
    </div>
</header>
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
