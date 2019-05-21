<?php
    // TODO: Move to composer
    $email = session('admin.user.username');
    $default = "mm";
    $size = 40;
    $grav_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?d=" . urlencode($default) . "&s=" . $size;
?><!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>{{ $title or "No Title"  }} | {{ucfirst($template)}}</title>
    <meta name="description" content="{{ $description or "No Description"  }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{ asset('/admin/templates/system/img/apple-touch-icon-114x114-precomposed.png') }}">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ asset('/admin/templates/system/img/apple-touch-icon-72x72-precomposed.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('/admin/templates/system/img/apple-touch-icon-57x57-precomposed.png') }}">
    <link rel="stylesheet" href="{{ elixir('admin/templates/system/css/all.css') }}">
    @include('admin.templates.system.css')

    @include('admin.templates.system.head-scripts')
    <script src="{{ elixir('admin/templates/system/js/head-scripts.js') }}"></script>

    {{-- needed for the theme to work--}}
    <script>Breakpoints();</script>

</head>
<body class="{{$bodyClasses}} site-menubar-unfold site-menubar-keep">

<!--[if lt IE 8]>
{!! trans('admin.outdated-browser') !!}
<![endif]-->

<nav id="main-header-outer" class="site-navbar navbar navbar-fixed-top navbar-mega" role="navigation">

    <div class="system-header-outer">
        <div class="container-fluid layout-boxed system-header">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle hamburger hamburger-close navbar-toggle-left hided" data-toggle="menubar">
                    <span class="sr-only">{{trans('admin.toggle-navigation')}}</span>
                    <span class="hamburger-bar"></span>
                </button>
                <button type="button" class="navbar-toggle collapsed" data-target="#site-navbar-collapse" data-toggle="collapse">
                    <i class="icon wb-more-horizontal" aria-hidden="true"></i>
                </button>
                <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
                    <a href="/admin"><img class="navbar-brand-logo" src="/admin/templates/{{$template}}/img/logo.png"></a>
                </div>
                <a class="navbar-avatar dropdown-toggle pull-right margin-15 avatar-small" data-toggle="dropdown" href="#" aria-expanded="false"
                   data-animation="scale-up" role="button">
                              <span class="avatar">

                                  <img src="{{ $grav_url }}" alt="">
                                <i></i>
                              </span>
                </a>
            </div>

            <div class="collapse navbar-collapse navbar-collapse-toolbar pull-right" id="site-navbar-collapse">
                <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right airangel-logout">
                    <li class="dropdown">
                        <a href="/logout" role="button" class="logout">
                            <i class="fa fa-power-off white font-size-15" aria-hidden="true"></i>
                        </a>
                    </li>
                </ul> {{--end airangel-logout--}}

                <ol class="nav navbar-right user-details">
                    <li class="username">{{ session('admin.user.username') }}</li>
                    <li class="user-role">{{ session('admin.user.role') }}</li>
                </ol> {{--end user-details --}}


            </div>
            <ul class="nav navbar-toolbar navbar-toolbar-right user-profile pull-right avatar-large">
                <li class="dropdown">
                    <a class="navbar-avatar dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"
                    data-animation="scale-up" role="button">
                        <span class="avatar">
                            <img src="{{ $grav_url }}" alt="">
                        </span>
                    </a>
                </li>
            </ul> {{--end user avatar--}}
        </div>
    </div>

    @can('access', 'manage-profile')
        @include('admin.templates.system.user_profile')
    @endcan

    @include('admin.templates.system.menus.breadcrumbs')
</nav> {{--end #main-header-section --}}

<div id="main-content-outer" class="layout-boxed">
    {{--{{$inactive_widgets}}--}}
    @include('admin.templates.system.menus.menu')
    <div class="page animsition">
        <div class="page-content container-fluid">
            {{--Showing the messages as a sweet alert--}}
            @include('admin.templates.system.messages')
            @yield('content')
			@include('admin.modal')
        </div>
    </div>

</div> {{--end #main-content-outer--}}

<footer id="footer-outer" class="site-footer">
    <div class="layout-boxed">
        <div class="site-footer-content">©{{date('Y')}} {{ucfirst($template)}}. {{trans('admin.all-rights-reserved')}}
            {{trans('admin.version')}}
            {{ config('app.version') }}

			{{-- No one but dev can see this as there is no permissions --}}
            @can('access', 'git-hash')
                | Git Commit Hash <a class="white" target="_blank" href="https://github.com/airangel/myairangel-v3/commit/{{ $gitFullHash }}">{{ strtoupper( $gitHash ) }}</a>
            @endcan
        </div>
    </div>
</footer> {{--end #footer-outer --}}

@include('admin.templates.system.loading_pages')
@include('admin.templates.system.js')

</body>
</html>
