@extends('layouts.docs')

@section('content')
    <a class="btn ghost docs-back" href="{{ route('docs.index') }}"><svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>Hujjatlar ro'yxati</a>

    @if (isset($current) && $current === 'installatsiya')
        <a class="btn" href="{{ route('docs.install') }}" download style="margin-bottom:20px">
            <svg viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
            install.php yuklab olish
        </a>
    @endif

    <div class="docs-layout">
        <aside class="docs-side">
            <h3>Hujjatlar</h3>
            @foreach ($docs as $name => $title)
                <a href="{{ route('docs.show', ['name' => $name]) }}" class="{{ $name === $current ? 'active' : '' }}">{{ $title }}</a>
            @endforeach
        </aside>

        <article class="prose">
            {!! $html !!}
        </article>
    </div>
@endsection