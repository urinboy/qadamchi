<?php
// routes/docs.php - Dokumentatsiya uchun route-lar

// Asosiy docs sahifasi
Route::get('/docs', function() {
    $content = file_get_contents(__DIR__ . '/../docs/README.md');
    $html = markdownToHtml($content);
    return view('docs.layout', ['content' => $html, 'title' => 'Qadamchi Dokumentatsiyasi']);
})->name('docs');

// Versiya bo'yicha docs
Route::get('/docs/{version}', function($version) {
    $file = __DIR__ . "/../docs/{$version}/index.md";
    if (!file_exists($file)) {
        return view('error', ['message' => 'Versiya topilmadi'], 'app');
    }
    $content = file_get_contents($file);
    $html = markdownToHtml($content);
    return view('docs.layout', ['content' => $html, 'title' => "Qadamchi {$version} Dokumentatsiyasi"]);
})->name('docs.version');

// Alohida sahifalar
Route::get('/docs/{version}/{page}', function($version, $page) {
    $file = __DIR__ . "/../docs/{$version}/{$page}.md";
    if (!file_exists($file)) {
        return view('error', ['message' => 'Sahifa topilmadi'], 'app');
    }
    $content = file_get_contents($file);
    $html = markdownToHtml($content);
    return view('docs.layout', ['content' => $html, 'title' => ucfirst($page) . " - Qadamchi {$version}"]);
})->name('docs.page');

// Oddiy Markdown to HTML funksiyasi
function markdownToHtml($markdown) {
    // Oddiy konvertatsiya (to'liq parser o'rniga)
    $html = htmlspecialchars($markdown);
    $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
    $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);
    $html = preg_replace('/`(.*?)`/', '<code>$1</code>', $html);
    $html = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $html);
    $html = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $html);
    $html = preg_replace('/^- (.*?)$/m', '<li>$1</li>', $html);
    $html = preg_replace('/(<li>.*?<\/li>)/s', '<ul>$1</ul>', $html);
    $html = nl2br($html);
    return $html;
}