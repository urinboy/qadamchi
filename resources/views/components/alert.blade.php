@php
    $type = $type ?? 'info';
    $class = ['success' => 'success', 'error' => 'danger', 'danger' => 'danger', 'info' => 'success'][$type] ?? 'success';
@endphp
<div class="alert {{ $class }}">{{ $slot ?? ($message ?? '') }}</div>