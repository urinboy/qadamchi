<!DOCTYPE html>
<html lang="uz"><head><meta charset="UTF-8"><title>Xatolik (debug)</title>
<style>body{font-family:monospace;background:#1e1e2e;color:#cdd6f4;padding:20px;line-height:1.5;margin:0}
.box{max-width:960px;margin:0 auto}.msg{background:#313244;padding:16px;border-radius:8px;color:#f38ba8;font-size:18px;margin-bottom:16px;white-space:pre-wrap;word-break:break-word}
pre{background:#11111b;padding:16px;border-radius:8px;overflow:auto;border:1px solid #45475a}
.title{color:#89b4fa;font-weight:bold;margin-top:24px;margin-bottom:8px}
.file{color:#a6e3a1}</style>
</head><body><div class="box">
<div class="msg">{{ $exception->getMessage() }}</div>
<div class="title">Fayl</div><pre class="file">{{ $exception->getFile() }}:{{ $exception->getLine() }}</pre>
<div class="title">Trace</div><pre>{{ $exception->getTraceAsString() }}</pre>
</div></body></html>