<?php
namespace Qadamchi\Support;

/**
 * Qo'lbola Markdown → HTML konverter (Composer'siz).
 *
 * Docs/ papkadagi .md fayllarni render qilish uchun. Qamrov:
 *  - heading #/##/###/#### (id + anchor link)
 *  - bold ** va italic * / _
 *  - inline code `...`
 *  - fenced code block ```lang ... ```  (code-bar + til yorlig'i + Nusxa tugmasi + syntax highlight)
 *  - blockquote >  (callout variantlari: **Eslatma/Ogohlantirish/Maslahat/Xato/...**)
 *  - GFM jadval (| a | b | + |---|---|)
 *  - unordered list - / *  va ordered list 1.
 *  - link [t](u) — faqat http/https/mailto scheme'lari (javascript: bloklanadi)
 *  - hr --- va paragraf
 *
 * Yordamchi statik metodlar:
 *  - Markdown::toc($md) — heading'lardan "On this page" TOC uchun massiv qaytaradi.
 *  - Markdown::highlight($code, $lang) — php/bash/json/env/ini uchun o'z tokenizer (span class'lari).
 *
 * Xavfsizlik: prose'da < > & escape qilinadi (inline HTML o'chirilgan);
 * code block ichkini to'liq escape qilinadi; href scheme whitelist.
 */
class Markdown
{
    /** Markdown matnni HTML'ga aylantiradi. */
    public static function toHtml(string $md): string
    {
        if ($md === '') {
            return '';
        }

        // CRLF → LF, ortiqcha bo'sh qatorlarni kamaytirish.
        $md = str_replace(["\r\n", "\r"], "\n", $md);
        $md = preg_replace('/\n{3,}/', "\n\n", $md);

        // 1) Fenced code block'larni ajratamiz — highlight + code-bar bilan placeholder'ga solamiz.
        $blocks = [];
        $md = preg_replace_callback('/^```([a-zA-Z0-9_+-]*)\n(.*?)\n```$/ms', function ($m) use (&$blocks) {
            $lang = strtolower($m[1]);
            $highlighted = self::highlight($m[2], $lang);
            $label = $lang !== '' ? $lang : 'text';
            $labelHtml = htmlspecialchars($label, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $id = "\x00B" . count($blocks) . "\x00";
            $blocks[$id] = '<div class="code" data-lang="' . $labelHtml . '">'
                . '<div class="code-bar">'
                . '<span class="code-lang">' . $labelHtml . '</span>'
                . '<button class="code-copy" type="button" aria-label="Kodni nusxalash">'
                . '<svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>'
                . '<span>Nusxa</span>'
                . '</button>'
                . '</div>'
                . '<pre><code>' . $highlighted . '</code></pre>'
                . '</div>';
            return $id;
        }, $md);

        $lines = explode("\n", $md);
        $html = [];
        $i = 0;
        $n = count($lines);

        while ($i < $n) {
            $line = $lines[$i];

            // Code block placeholder.
            if (preg_match('/^\x00B\d+\x00$/', $line)) {
                $html[] = $blocks[$line];
                $i++;
                continue;
            }

            // Bo'sh qator.
            if (trim($line) === '') {
                $i++;
                continue;
            }

            // Horizontal rule: --- *** ___
            if (preg_match('/^\s*([-*_])(\s*\1){2,}[\s\1]*$/', $line)) {
                $html[] = '<hr>';
                $i++;
                continue;
            }

            // Heading: # .. ####  (id + anchor)
            if (preg_match('/^(#{1,4})\s+(.*)$/', $line, $m)) {
                $level = strlen($m[1]);
                $slug = self::slugify($m[2]);
                $anchor = $level <= 3
                    ? '<a class="anchor" href="#' . $slug . '" aria-label="Bo\'limga havola" title="Bo\'limga havola">#</a>'
                    : '';
                $html[] = '<h' . $level . ' id="' . $slug . '">' . self::inline($m[2]) . $anchor . '</h' . $level . '>';
                $i++;
                continue;
            }

            // Blockquote: > ... (ketma-ket qatorlar). Callout variantlari aniqlanadi.
            if (preg_match('/^>\s?(.*)$/', $line, $m)) {
                $buf = [];
                while ($i < $n && preg_match('/^>\s?(.*)$/', $lines[$i], $mm)) {
                    $buf[] = $mm[1];
                    $i++;
                }
                $content = implode(' ', $buf);
                $callout = self::detectCallout($content);
                if ($callout !== null) {
                    $html[] = '<blockquote class="callout callout-' . $callout['type'] . '"><p>'
                        . $callout['label'] . ' ' . self::inline($callout['rest']) . '</p></blockquote>';
                } else {
                    $html[] = '<blockquote>' . self::inline($content) . '</blockquote>';
                }
                continue;
            }

            // GFM jadval: sarlavha qatori + separator qatori.
            if (strpos($line, '|') !== false
                && $i + 1 < $n
                && self::isTableSeparator($lines[$i + 1])) {
                $header = self::splitRow($line);
                $i += 2;
                $body = [];
                while ($i < $n && strpos($lines[$i], '|') !== false && trim($lines[$i]) !== '') {
                    $body[] = self::splitRow($lines[$i]);
                    $i++;
                }
                $html[] = self::buildTable($header, $body);
                continue;
            }

            // Unordered list: - / *
            if (preg_match('/^\s*[-*]\s+(.*)$/', $line, $m)) {
                $items = [];
                while ($i < $n && preg_match('/^\s*[-*]\s+(.*)$/', $lines[$i], $mm)) {
                    $items[] = self::inline($mm[1]);
                    $i++;
                }
                $html[] = '<ul><li>' . implode('</li><li>', $items) . '</li></ul>';
                continue;
            }

            // Ordered list: 1.
            if (preg_match('/^\s*\d+\.\s+(.*)$/', $line, $m)) {
                $items = [];
                while ($i < $n && preg_match('/^\s*\d+\.\s+(.*)$/', $lines[$i], $mm)) {
                    $items[] = self::inline($mm[1]);
                    $i++;
                }
                $html[] = '<ol><li>' . implode('</li><li>', $items) . '</li></ol>';
                continue;
            }

            // Paragraf — yangi blok boshlanmaguncha qatorlarni yig'amiz.
            $buf = [$line];
            $i++;
            while ($i < $n && !self::startsBlock($lines, $i)) {
                $buf[] = $lines[$i];
                $i++;
            }
            $html[] = '<p>' . self::inline(implode(' ', $buf)) . '</p>';
        }

        return implode("\n", $html);
    }

    /**
     * Heading'lardan "On this page" TOC uchun massiv qaytaradi (faqat h2/h3).
     * Har element: ['level'=>int, 'text'=>string, 'slug'=>string].
     */
    public static function toc(string $md): array
    {
        if ($md === '') {
            return [];
        }
        $md = str_replace(["\r\n", "\r"], "\n", $md);
        // Fenced code bloklarni o'tkazib yuboramiz (ichida # bo'lishi mumkin).
        $md = preg_replace('/^```[^\n]*\n.*?\n```/ms', '', $md);

        $toc = [];
        foreach (explode("\n", $md) as $line) {
            if (preg_match('/^(#{2,3})\s+(.*)$/', $line, $m)) {
                $toc[] = [
                    'level' => strlen($m[1]),
                    'text'  => self::plainText($m[2]),
                    'slug'  => self::slugify($m[2]),
                ];
            }
        }
        return $toc;
    }

    /**
     * Syntax highlighting — php/bash/sh/shell/json/env/ini uchun o'z tokenizer.
     * Kirish: xom kod (escape qilinmagan). Chiqish: HTML span'lar (escape qilingan).
     * Noma'lum til → oddiy escape.
     */
    public static function highlight(string $code, string $lang): string
    {
        $lang = strtolower(trim($lang));
        if (!in_array($lang, ['php', 'bash', 'sh', 'shell', 'json', 'env', 'ini'], true)) {
            return htmlspecialchars($code, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        // Protected tokenlar (comment + string) — placeholder'ga olib, tayyor HTML span saqlaymiz.
        // Placeholder faqat HARFLARDAN (raqamlar yo'q) — token regex'lari (number/keyword/fn) tegmaydi.
        $ph = [];
        $stash = function (array $m, string $cls) use (&$ph): string {
            $i = count($ph);
            // base26 harf: 0->a, 1->b, ..., 26->aa ...
            $tag = '';
            $n = $i;
            do { $tag = chr(97 + ($n % 26)) . $tag; $n = intdiv($n, 26) - 1; } while ($n >= 0);
            $id = "\x02" . $tag . "\x02";
            $ph[$id] = '<span class="' . $cls . '">'
                . htmlspecialchars($m[0], ENT_QUOTES | ENT_HTML5, 'UTF-8')
                . '</span>';
            return $id;
        };

        // 1) Comment + string — bitta leftmost-match regex (alternatsiya).
        //    Eng chap token tanlanadi — string ichidagi # yoki // comment deb olinmaydi.
        $isCom = static function (string $t): bool {
            return $t[0] === '/' || $t[0] === '#' || $t[0] === ';';
        };
        if ($lang === 'php') {
            $pat = '/\/\*[\s\S]*?\*\/|\/\/[^\n]*|#[^\n]*|"(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'/';
        } elseif ($lang === 'json') {
            $pat = '/"(?:\\\\.|[^"\\\\])*"/s';
        } elseif ($lang === 'env' || $lang === 'ini') {
            $pat = '/^[ \t]*(?:;|#)[^\n]*|"(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'/m';
            $isCom = static function (string $t): bool {
                return $t[0] === ';' || $t[0] === '#';
            };
        } else { // bash / sh / shell
            $pat = '/^[ \t]*#[^\n]*|"(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'/m';
            $isCom = static function (string $t): bool {
                return $t[0] === '#';
            };
        }
        $code = preg_replace_callback($pat, fn($m) => $stash($m, $isCom($m[0]) ? 't-com' : 't-str'), $code);

        // 2) Qolgan matnni escape qilamiz (placeholder \x02... \x02 buzilmaydi).
        $code = htmlspecialchars($code, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 3) Til bo'yicha token bo'yash (escaped code ustida).
        if ($lang === 'php') {
            $kw = 'abstract|and|array|as|break|callable|case|catch|class|clone|const|continue|declare|default|do|echo|else|elseif|empty|enddeclare|endfor|endforeach|endif|endswitch|endwhile|extends|final|finally|fn|for|foreach|function|global|goto|if|implements|include_once|include|instanceof|insteadof|interface|isset|list|match|namespace|new|or|print|private|protected|public|readonly|require_once|require|return|static|switch|throw|trait|try|unset|use|var|while|xor|yield|true|false|null|self|parent';
            $code = preg_replace('/\b(' . $kw . ')\b/', '<span class="t-kw">$1</span>', $code);
            $code = preg_replace('/(\$[a-zA-Z_]\w*)/', '<span class="t-var">$1</span>', $code);
            $code = preg_replace('/\b(\d+(?:\.\d+)?)\b/', '<span class="t-num">$1</span>', $code);
            $code = preg_replace('/\b([a-zA-Z_]\w*)(\s*\()/', '<span class="t-fn">$1</span>$2', $code);
        } elseif ($lang === 'json') {
            $code = preg_replace('/\b(true|false|null)\b/', '<span class="t-kw">$1</span>', $code);
            $code = preg_replace('/\b(\d+(?:\.\d+)?)\b/', '<span class="t-num">$1</span>', $code);
        } elseif ($lang === 'env' || $lang === 'ini') {
            $code = preg_replace('/^([A-Za-z_][A-Za-z0-9_]*)(=)/m', '<span class="t-key">$1</span>$2', $code);
            $code = preg_replace('/\b(true|false|null|on|off|yes|no)\b/i', '<span class="t-kw">$1</span>', $code);
        } else { // bash / sh / shell
            $cmds = 'php|chmod|cd|mkdir|rmdir|rm|cp|mv|ls|cat|echo|printf|sudo|git|composer|export|source|curl|wget|tar|gzip|grep|sed|awk|systemctl|service|apt|brew|npm|node';
            $code = preg_replace('/^([ \t]*)(' . $cmds . ')\b/m', '$1<span class="t-fn">$2</span>', $code);
            $code = preg_replace('/(^|[ \t])(--?[a-zA-Z][a-zA-Z0-9-]*)/', '$1<span class="t-key">$2</span>', $code);
            $code = preg_replace('/\b(\d+(?:\.\d+)?)\b/', '<span class="t-num">$1</span>', $code);
        }

        // 4) Placeholder'larni tiklash (tayyor HTML span bilan almashtirish).
        return strtr($code, $ph);
    }

    /**
     * Blockquote ichidan callout aniqlaydi.
     * `> **Eslatma:** matn` → ['type'=>'note','label'=>'<strong>Eslatma:</strong>','rest'=>'matn'].
     * Oddiy bo'lsa → null.
     */
    private static function detectCallout(string $content): ?array
    {
        $map = [
            'Eslatma'        => 'note',
            'Talab'          => 'note',
            'Maslahat'       => 'tip',
            'Ogohlantirish'  => 'warn',
            'Diqqat'         => 'warn',
            'Muhim'          => 'warn',
            'Xato'           => 'danger',
        ];
        $keys = implode('|', array_keys($map));
        if (preg_match('/^\*\*(' . $keys . ')(:?)\*\*\s*(.*)$/u', $content, $m)) {
            $word = $m[1];
            $colon = $m[2];
            $rest = $m[3];
            $label = '<strong class="callout-label">' . htmlspecialchars($word . $colon, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</strong>';
            return ['type' => $map[$word], 'label' => $label, 'rest' => $rest];
        }
        return null;
    }

    /** Heading matnidan URL slug yasaydi (a-z0-9-, lotinlashtirishsiz). */
    private static function slugify(string $text): string
    {
        $text = self::plainText($text);
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim($text, '-');
        return $text !== '' ? $text : 'section';
    }

    /** Markdown inline belgilarni toza matnga aylantiradi (TOC matni uchun). */
    private static function plainText(string $text): string
    {
        $text = preg_replace('/`([^`]+)`/', '$1', $text);              // inline code
        $text = preg_replace('/\[([^\]]+)\]\([^)]*\)/', '$1', $text);  // link
        $text = preg_replace('/\*\*([^*]+)\*\*/', '$1', $text);        // bold
        $text = preg_replace('/\*([^*]+)\*|_([^_]+)_/', '$1$2', $text); // italic
        return trim($text);
    }

    /** Qator jadval separatori (|:---|---:| kabi). */
    private static function isTableSeparator(string $line): bool
    {
        $t = trim($line);
        if ($t === '' || strpos($t, '-') === false) {
            return false;
        }
        // Faqat |, :, -, bo'shliq.
        return preg_match('/^[\s|:-]+$/', $t) === 1 && strpos($t, '|') !== false;
    }

    /** Jadval qatorini | bo'yicha bo'ladi (chekka | lar olib tashlanadi). */
    private static function splitRow(string $line): array
    {
        $line = trim($line);
        $line = trim($line, '|');
        $cells = explode('|', $line);
        return array_map('trim', $cells);
    }

    /** <table> yig'adi. */
    private static function buildTable(array $header, array $body): string
    {
        $out = '<table><thead><tr>';
        foreach ($header as $cell) {
            $out .= '<th>' . self::inline($cell) . '</th>';
        }
        $out .= '</tr></thead><tbody>';
        foreach ($body as $row) {
            $out .= '<tr>';
            foreach ($row as $cell) {
                $out .= '<td>' . self::inline($cell) . '</td>';
            }
            $out .= '</tr>';
        }
        $out .= '</tbody></table>';
        return $out;
    }

    /** $lines[$i] yangi blok boshlanadimi (paragrafni to'xtatish uchun). */
    private static function startsBlock(array $lines, int $i): bool
    {
        if (!isset($lines[$i])) {
            return true;
        }
        $line = $lines[$i];
        if (trim($line) === '') {
            return true;
        }
        if (preg_match('/^\x00B\d+\x00$/', $line)) {
            return true;
        }
        if (preg_match('/^#{1,4}\s+/', $line)) {
            return true;
        }
        if (preg_match('/^>\s?/', $line)) {
            return true;
        }
        if (preg_match('/^\s*[-*]\s+/', $line)) {
            return true;
        }
        if (preg_match('/^\s*\d+\.\s+/', $line)) {
            return true;
        }
        if (preg_match('/^\s*([-*_])(\s*\1){2,}[\s\1]*$/', $line)) {
            return true;
        }
        // Jadval sarlavhasi + keyingi separator.
        if (strpos($line, '|') !== false && isset($lines[$i + 1]) && self::isTableSeparator($lines[$i + 1])) {
            return true;
        }
        return false;
    }

    /** Inline Markdown: escape → code span → link → bold → italic → code'ni qayta tiklash. */
    private static function inline(string $text): string
    {
        // 1) HTML escape (inline HTML bloklanadi).
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 2) Inline code span — bold/italic'dan himoyalash uchun placeholder'ga olamiz.
        $codes = [];
        $text = preg_replace_callback('/`([^`]+)`/', function ($m) use (&$codes) {
            $id = "\x01C" . count($codes) . "\x01";
            $codes[$id] = '<code>' . htmlspecialchars($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</code>';
            return $id;
        }, $text);

        // 3) Link [t](u) — http/https/mailto (tashqi), yoki ichki doc havolasi.
        $text = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)\)/', function ($m) {
            $url = trim($m[2]);
            $label = $m[1];

            // a) Tashqi havola — http/https/mailto (target=_blank).
            if (preg_match('#^(https?:|mailto:)#i', $url)) {
                return '<a href="' . $url . '" target="_blank" rel="noopener">' . $label . '</a>';
            }

            // b) Shu sahifa anchor'i: #slug.
            if ($url !== '' && $url[0] === '#') {
                return '<a href="' . $url . '">' . $label . '</a>';
            }

            // c) Ichki doc havolasi — xavfsiz relative path (scheme yo'q, // emas).
            //    `docs/foo.md`, `foo.md`, `foo`, `foo#slug`, `../docs/foo.md` → /docs/foo[#slug]
            if ($url !== '' && !str_starts_with($url, '//') &&
                preg_match('~^[a-z0-9_./-]*(?:#[a-z0-9_-]*)?$~i', $url)) {
                $frag = '';
                $path = $url;
                $hpos = strpos($path, '#');
                if ($hpos !== false) {
                    $frag = substr($path, $hpos);
                    $path = substr($path, 0, $hpos);
                }
                // `docs/` yoki `../docs/` prefiksini olib tashlaymiz.
                $path = preg_replace('#^(?:\.\./)*(?:docs/)?#', '', $path);
                // `.md` kengaytmasini olib tashlaymiz.
                $path = preg_replace('#\.md$#', '', $path);
                if ($path !== '') {
                    return '<a href="/docs/' . $path . $frag . '">' . $label . '</a>';
                }
            }

            // d) Ruxsat etilmagan scheme (javascript:, data: va h.k.) — faqat matn.
            return $label;
        }, $text);

        // 4) Bold **...**
        $text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text);

        // 5) Italic *...* va _..._ (so'z ichidagi bo'lmasin).
        $text = preg_replace('/(?<!\w)\*([^*\n]+)\*(?!\w)/', '<em>$1</em>', $text);
        $text = preg_replace('/(?<!\w)_([^_\n]+)_(?!\w)/', '<em>$1</em>', $text);

        // 6) Code span'larni qayta tiklash.
        if ($codes) {
            $text = strtr($text, $codes);
        }

        return $text;
    }
}