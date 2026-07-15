@extends('layouts.docs')

@section('content')
    <div class="docs-head">
        <h1>Hujjatlar</h1>
        <p class="muted">Qadamchi mikrofreymvorki bo'yicha qo'llanmalar va yo'riqnomalar.</p>
    </div>

    <div class="docs-grid">
        @foreach ($docs as $name => $title)
            <a class="docs-card" href="{{ route('docs.show', ['name' => $name]) }}">
                <span class="docs-card-main">
                    <svg class="docs-card-icon" viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                    <span class="docs-card-title">{{ $title }}</span>
                </span>
                <span class="docs-card-arrow">→</span>
            </a>
        @endforeach
    </div>
@endsection