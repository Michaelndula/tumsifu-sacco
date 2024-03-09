@extends('user.layouts.master')

@section('content')
    <!-- page-wrapper start -->
    <div class="page-wrapper default-version">
        @include('user.partials.sidenav')
        @include('user.partials.topnav')

        <div class="body-wrapper">
            <div class="bodywrapper__inner">

                @include('user.partials.breadcrumb')

                @yield('panel')


            </div><!-- bodywrapper__inner end -->
        </div><!-- body-wrapper end -->
    </div>

@endsection
