@extends('errors::minimal')
@php
$log = storage_path() . '/logs/laravel.log';
if (file_exists($log)) {
    echo file_get_contents($log);
}
@endphp
@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server Error'))
