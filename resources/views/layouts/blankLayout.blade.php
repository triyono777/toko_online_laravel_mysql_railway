@extends('layouts/commonMaster')

@section('layoutContent')
<!-- Content -->
@include('_partials.flash-messages')
@yield('content')
<!--/ Content -->
@endsection
