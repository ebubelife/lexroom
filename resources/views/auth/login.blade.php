@extends('layouts.auth')

@section('title', 'Log in to First Mediator — AI-assisted dispute resolution')
@section('description', 'Log in to your First Mediator account to manage your dispute sessions, evidence vault, and mediation reports.')

@section('content')
    @include('auth.partials.tabbed-form', ['activeTab' => 'signin'])
@endsection
