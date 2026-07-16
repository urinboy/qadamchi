@extends('layouts.app')

@section('container_class', 'fluid')

@section('content')
@php
    $initial = mb_strtoupper(mb_substr($user->name ?? '?', 0, 1));
    $joined = $user->created_at ? date('d.m.Y', strtotime($user->created_at)) : '—';
@endphp

<div class="dash">
    <header class="dash-head">
        <div>
            <h1>Dashboard</h1>
            <p class="sub">Bu sahifa <code>auth</code> middleware bilan himoyalangan — faqat kirgan foydalanuvchi ko'radi. Boshqa hech kim bu yerga yetib bira olmaydi.</p>
        </div>
        <span class="dash-badge">
            <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
            auth · himoyalangan
        </span>
    </header>

    <section class="dash-id">
        <div class="dash-id-head">
            <div class="dash-avatar">{{ $initial }}</div>
            <div class="dash-id-main">
                <div class="dash-name">{{ $user->name }}</div>
                <div class="dash-id-email">
                    <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    <span>{{ $user->email }}</span>
                </div>
            </div>
        </div>
        <div class="dash-id-meta">
            <div class="dim">
                <span class="dim-label">ID</span>
                <span class="dim-val">#{{ $user->id }}</span>
            </div>
            <div class="dim">
                <span class="dim-label">Ro'yxatdan o'tgan</span>
                <span class="dim-val">{{ $joined }}</span>
            </div>
            <div class="dim">
                <span class="dim-label">Sessiya</span>
                <span class="dim-val">Kirilgan</span>
            </div>
        </div>
    </section>

    <section class="dash-section">
        <h2>Bu namuna nima ko'rsatadi</h2>
        <div class="dash-features">
            <div class="dash-feat">
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                <div class="dash-feat-body">
                    <div class="dash-feat-title">Auth middleware</div>
                    <div class="dash-feat-desc">Faqat kirgan foydalanuvchi ko'radi — aks holda <code>/login</code>'ga yo'naltiradi.</div>
                </div>
            </div>
            <div class="dash-feat">
                <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6z"/></svg>
                <div class="dash-feat-body">
                    <div class="dash-feat-title">Session-based Auth</div>
                    <div class="dash-feat-desc"><code>Auth::attempt/logout</code>, session regenerate, <code>auth</code>/<code>guest</code> direktivlari.</div>
                </div>
            </div>
            <div class="dash-feat">
                <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                <div class="dash-feat-body">
                    <div class="dash-feat-title">Eloquent'ga o'xshash Model</div>
                    <div class="dash-feat-desc"><code>User</code> modeli — <code>fillable</code>, <code>hidden</code>, <code>timestamps</code>.</div>
                </div>
            </div>
            <div class="dash-feat">
                <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                <div class="dash-feat-body">
                    <div class="dash-feat-title">Blade + layout</div>
                    <div class="dash-feat-desc"><code>@extends</code>, <code>@section</code>, <code>@yield</code> — layout inheritance.</div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection