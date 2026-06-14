@extends('emails.layouts.master')
@section('subject-strip') {{ $headerIcon ?? '' }} {{ $headerTitle ?? 'Notification' }} @endsection
@php $__stripTheme = $stripTheme ?? 'blue'; @endphp
@section('subject-theme', $__stripTheme)
@section('content')
