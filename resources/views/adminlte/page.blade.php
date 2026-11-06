@extends('adminlte::page')

@section('adminlte_css_pre')
    @parent
    <link rel="icon" type="image/jpeg" href="{{ asset('vendor/adminlte/dist/img/logo.jpeg') }}">
@stop

@section('adminlte_css')

    @parent

    <link rel="stylesheet" href="{{ asset('css/wms.css') }}">

@stop
