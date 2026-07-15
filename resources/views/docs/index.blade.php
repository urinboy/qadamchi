@extends('layouts.docs')

@section('content')
@php
$icons = [
    'download' => 'M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z',
    'book'     => 'M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z',
    'map'      => 'M20.5 3l-.16.03L15 5.1 9 3 3.36 4.9c-.21.07-.36.25-.36.48V20.5c0 .28.22.5.5.5l.16-.03L9 18.9l6 2.1 5.64-1.9c.21-.07.36-.25.36-.48V3.5c0-.28-.22-.5-.5-.5zM15 19l-6-2.11V5l6 2.11V19z',
    'exchange' => 'M16 17.01V10h-2v7.01h-3L15 21l4-3.99h-3zM8 8.99V16h2V8.99h3L9 5 5 8.99h3z',
    'list'     => 'M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z',
    'terminal' => 'M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z',
    'tree'     => 'M22 11V3h-7v2h5v4h-5v2h5v4h-5v2h7v-8h-3zm-9 0V3H6v2h5v4H6v2h5v4H6v2h7v-8h-3z',
    'history'  => 'M13 3a9 9 0 0 0-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42A8.96 8.96 0 0 0 13 21a9 9 0 0 0 0-18zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z',
    'archive'  => 'M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5L6.5 12H10v-2h4v2h3.5L12 17.5z',
];
@endphp

<div class="docs-hero">
    <h1>Hujjatlar</h1>
    <p>Qadamchi mikrofreymvorki bo'yicha qo'llanmalar, yo'riqnomalar va reference.</p>
</div>

<div class="docs-search-wrap">
    <svg class="docs-search-icon" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0A4.5 4.5 0 1 1 14 9.5 4.5 4.5 0 0 1 9.5 14z"/></svg>
    <input id="docs-search" class="docs-search" type="search" placeholder="Hujjatlarni qidirish..." autocomplete="off">
</div>

@foreach ($groups as $group)
    <section class="docs-cat">
        <h2 class="docs-cat-title">{{ $group['cat'] }}</h2>
        <div class="docs-grid">
            @foreach ($group['items'] as $name => $meta)
                <a class="docs-card" href="{{ route('docs.show', ['name' => $name]) }}"
                   data-title="{{ $meta['title'] }}" data-desc="{{ $meta['desc'] ?? '' }}">
                    <span class="docs-card-top">
                        <svg class="docs-card-icon" viewBox="0 0 24 24"><path d="{{ $icons[$meta['icon']] ?? $icons['book'] }}"></svg>
                        <span class="docs-card-title">{{ $meta['title'] }}</span>
                    </span>
                    <span class="docs-card-desc">{{ $meta['desc'] ?? '' }}</span>
                </a>
            @endforeach
        </div>
    </section>
@endforeach

<div class="docs-no-results">Hech narsa topilmadi. Boshqa so'z bilan urinib ko'ring.</div>
@endsection