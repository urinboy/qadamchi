@extends('layouts.app')

@section('container_class', 'wide')

@section('content')
    <div class="card">
        <h1>Dashboard</h1>
        <p class="sub">Bu sahifa <code>auth</code> middleware bilan himoyalangan — faqat kirgan user ko'radi.</p>

        <div class="field">
            <label>Ism</label>
            <p class="value">{{ $user->name }}</p>
        </div>
        <div class="field">
            <label>Email</label>
            <p class="value">{{ $user->email }}</p>
        </div>
        <div class="field">
            <label>ID</label>
            <p class="value">{{ $user->id }}</p>
        </div>

        <div class="row">
            <a class="btn ghost" href="{{ route('home') }}"><svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>Bosh sahifaga qaytish</a>
            <a class="btn ghost" href="{{ route('docs.index') }}"><svg viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>Hujjatlar</a>
        </div>
    </div>
@endsection