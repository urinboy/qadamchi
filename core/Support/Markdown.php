<?php
namespace Qadamchi\Support;

/**
 * Qo'lbola Markdown → HTML konverter (Composer'siz).
 *
 * Docs/ papkadagi .md fayllarni render qilish uchun. Qamrov:
 *  - heading #/##/###/####
 *  - bold ** va italic * / _
 *  - inline code `...`
 *  - fenced code block ```lang ... ```
 *  - blockquote >
 *  - GFM jadval (| a | b | + |---|---|)
 *  - unordered list - / *  va ordered list 1.
 *  - link [t](u) — faqat http/https/mailto scheme'lari (javascript: bloklanadi)
 *  - hr --- va paragraf
 *
 * Xavfsizlik: prose'da < > & escape qilinadi (inline HTML o'chiriladi);
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

        // 1) Fenced code block'larni ajratamiz — ichkini to'liq escape qilib placeholder'ga solamiz.
        $blocks = [];
        $md = preg_replace_callback('/^```[^\n]*\n(.*?)\n```$/ms', function ($m) use (&$blocks) {
            $code = htmlspecialchars($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $id = "\x00B" . count($blocks) . "\x00";
            $blocks[$id] = '<pre><code>' . $code . '</code></pre>';
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

            // Heading: # .. ####
            if (preg_match('/^(#{1,4})\s+(.*)$/', $line, $m)) {
                $level = strlen($m[1]);
                $html[] = '<h' . $level . '>' . self::inline($m[2]) . '</h' . $level . '>';
                $i++;
                continue;
            }

            // Blockquote: > ... (ketma-ket qatorlar).
            if (preg_match('/^>\s?(.*)$/', $line, $m)) {
                $buf = [];
                while ($i < $n && preg_match('/^>\s?(.*)$/', $lines[$i], $mm)) {
                    $buf[] = $mm[1];
                    $i++;
                }
                $html[] = '<blockquote>' . self::inline(implode(' ', $buf)) . '</blockquote>';
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

        // 3) Link [t](u) — faqat http/https/mailto.
        $text = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)\)/', function ($m) {
            $url = trim($m[2]);
            if (!preg_match('#^(https?:|mailto:)#i', $url)) {
                // ruxsat etilmagan scheme (javascript: va h.k.) — faqat matnni qoldiramiz.
                return $m[1];
            }
            return '<a href="' . $url . '" target="_blank" rel="noopener">' . $m[1] . '</a>';
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