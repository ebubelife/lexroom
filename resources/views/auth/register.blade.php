@extends('layouts.auth')

@section('title', 'Create your First Mediator account — Resolve disputes without a lawyer')
@section('description', 'Sign up to First Mediator and start resolving disputes with AI-assisted mediation. No lawyer needed. Setup in 15 minutes.')

@section('content')
    @include('auth.partials.tabbed-form', ['activeTab' => 'signup', 'prefillEmail' => $prefillEmail ?? null])
@endsection
