@extends('layouts.docs')

@section('content')
<nav class="breadcrumb">
    <a href="{{ route('docs.index') }}">Hujjatlar</a>
    <span class="sep">/</span>
    <span class="current">{{ $title }}</span>
</nav>

<div class="docs-layout">
    <aside class="docs-side">
        <h3>Hujjatlar</h3>
        @foreach ($docs as $name => $meta)
            <a href="{{ route('docs.show', ['name' => $name]) }}" class="{{ $name === $current ? 'active' : '' }}">{{ $meta['title'] }}</a>
        @endforeach
    </aside>

    <article class="prose">
        <header class="docs-article-head">
            <h1>{{ $title }}</h1>
            <div class="docs-meta">
                <span class="docs-ver">v{{ \Qadamchi\Support\Version::VERSION }}</span>
                @if (!empty($meta['cat']))<span class="muted">{{ $meta['cat'] }}</span>@endif
            </div>
        </header>

        @if ($current === 'installatsiya')
            <a class="btn" href="{{ route('docs.install') }}" download style="margin-bottom:20px">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                install.php yuklab olish
            </a>
        @endif

        {!! $html !!}

        <nav class="prev-next">
            @if ($prev)
                <a href="{{ route('docs.show', ['name' => $prev['name']]) }}">
                    <span class="pn-label">← Oldingi</span>
                    <span class="pn-title">{{ $prev['title'] }}</span>
                </a>
            @else
                <span class="pn-disabled"></span>
            @endif
            @if ($next)
                <a class="next" href="{{ route('docs.show', ['name' => $next['name']]) }}">
                    <span class="pn-label">Keyingi →</span>
                    <span class="pn-title">{{ $next['title'] }}</span>
                </a>
            @else
                <span class="pn-disabled"></span>
            @endif
        </nav>
    </article>

    <aside class="docs-toc">
        <h4>Sahifa bo'limlari</h4>
        @if (count($toc) > 0)
            <ul>
                @foreach ($toc as $item)
                    <li class="{{ $item['level'] === 3 ? 'toc-h3' : 'toc-h2' }}">
                        <a href="#{{ $item['slug'] }}">{{ $item['text'] }}</a>
                    </li>
                @endforeach
            </ul>
        @else
            <span class="docs-toc-empty">Bo'limlar yo'q.</span>
        @endif
    </aside>
</div>
@endsection