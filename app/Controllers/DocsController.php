<?php
namespace App\Controllers;

use Qadamchi\Http\Controller;
use Qadamchi\Http\Request;
use Qadamchi\Http\Response;
use Qadamchi\Support\Markdown;
use Qadamchi\Exceptions\RouteNotFoundException;

/**
 * Hujjatlar viewer — docs/ papkadagi markdown fayllarni render qiladi.
 *
 * docs/ public/ tashqarisida bo'lgani uchun HTTP orqali to'g'ridan-to'g'ri
 * yetib bormaydi; shu controller faylni o'qib Markdown::toHtml() bilan chiqaradi.
 * Whitelist path-traversal'dan himoya qiladi (faqat ruxsat etilgan nomlar).
 */
class DocsController extends Controller
{
    /** Kategoriya tartibi (index'da shu ketma-ketlikda chiqadi). */
    private array $catOrder = ['Boshlash', 'Yo\'riqnomalar', 'Reference', 'Tarix'];

    /** Ruxsat etilgan hujjatlar: fayl nomi => metadata (title/desc/cat/icon). */
    private array $docs = [
        // Boshlash
        'installatsiya' => [
            'title' => "O'rnatish — install.php",
            'desc'  => 'Bitta-fayl installer, .htaccess, SQLite/MySQL sozlash.',
            'cat'   => 'Boshlash',
            'icon'  => 'download',
        ],
        'tushunchalar' => [
            'title' => "Tushunchalar bo'yicha qo'llanma",
            'desc'  => 'Har tushuncha + "Laravel\'da bu..." eslatmasi.',
            'cat'   => 'Boshlash',
            'icon'  => 'book',
        ],
        // Yo'riqnomalar
        'laravel-otish' => [
            'title' => "Qadamchi → Laravel o'tish",
            'desc'  => 'Qadamchi\'da o\'rganib, Laravel\'ga oson o\'tish xaritasi.',
            'cat'   => 'Yo\'riqnomalar',
            'icon'  => 'map',
        ],
        'a-b-otish' => [
            'title' => 'A → B versiyasiga o\'tish',
            'desc'  => 'Composer\'siz (A) → Composer (B) bosqichlari, seam\'lar.',
            'cat'   => 'Yo\'riqnomalar',
            'icon'  => 'exchange',
        ],
        // Reference
        'qadamchi-commands' => [
            'title' => 'Qadamchi CLI buyruqlari',
            'desc'  => 'Barcha CLI buyruqlari — to\'liq reference jadvali.',
            'cat'   => 'Reference',
            'icon'  => 'list',
        ],
        'qadamchi_cli' => [
            'title' => 'Qadamchi CLI qo\'llanma',
            'desc'  => 'CLI binary: joylash, shebang, ruxsat, kengaytirish.',
            'cat'   => 'Reference',
            'icon'  => 'terminal',
        ],
        'tuzilma' => [
            'title' => 'Loyiha tuzilmasi',
            'desc'  => 'Joriy versiya (3.2.0) loyiha strukturasi — to\'liq daraxt.',
            'cat'   => 'Reference',
            'icon'  => 'tree',
        ],
        // Tarix
        'tarix' => [
            'title' => 'Qadamchi tarixi',
            'desc'  => 'Eng boshidan hozirgacha — versiyalar bo\'yicha to\'liq tarix.',
            'cat'   => 'Tarix',
            'icon'  => 'history',
        ],
        'qadamchi_v2.1' => [
            'title' => 'Qadamchi v2.1 strukturasi',
            'desc'  => 'Tarixiy arxiv — v2.1 dagi fayl joylashuvi.',
            'cat'   => 'Tarix',
            'icon'  => 'archive',
        ],
    ];

    /** Kategoriya bo'yicha guruhlangan hujjatlar (index uchun). */
    private function groups(): array
    {
        $groups = [];
        foreach ($this->catOrder as $cat) {
            $items = [];
            foreach ($this->docs as $name => $meta) {
                if (($meta['cat'] ?? '') === $cat) {
                    $items[$name] = $meta;
                }
            }
            if ($items) {
                $groups[] = ['cat' => $cat, 'items' => $items];
            }
        }
        return $groups;
    }

    /** Berilgan hujjatning qo'shnilari (prev/next) metadata bo'yicha tartibda. */
    private function neighbors(string $name): array
    {
        $order = array_keys($this->docs);
        $idx = array_search($name, $order, true);
        $prev = $idx > 0 ? $this->docLink($order[$idx - 1]) : null;
        $next = ($idx !== false && $idx < count($order) - 1) ? $this->docLink($order[$idx + 1]) : null;
        return [$prev, $next];
    }

    private function docLink(string $name): ?array
    {
        if (!isset($this->docs[$name])) {
            return null;
        }
        return ['name' => $name, 'title' => $this->docs[$name]['title']];
    }

    /** Hujjatlar ro'yxati (TOC). */
    public function index()
    {
        return view('docs.index', [
            'docs'   => $this->docs,
            'groups' => $this->groups(),
        ]);
    }

    /** Bitta hujjatni render qiladi. */
    public function show(Request $request)
    {
        $name = $request->routeParam('name');

        // Trailing .md bo'lsa olib tashlaymiz (eski linklar uchun).
        if (substr($name, -3) === '.md') {
            $name = substr($name, 0, -3);
        }

        // Whitelist tekshiruvi — yo'q bo'lsa 404 (path traversal blok).
        if (!isset($this->docs[$name])) {
            throw new RouteNotFoundException("docs/{$name}");
        }

        $path = base_path('docs/' . $name . '.md');
        $md = is_file($path) ? file_get_contents($path) : '';
        $html = Markdown::toHtml($md);
        $toc = Markdown::toc($md);
        [$prev, $next] = $this->neighbors($name);

        return view('docs.show', [
            'html'    => $html,
            'toc'     => $toc,
            'name'    => $name,
            'current' => $name,
            'title'   => $this->docs[$name]['title'],
            'meta'    => $this->docs[$name],
            'docs'    => $this->docs,
            'prev'    => $prev,
            'next'    => $next,
        ]);
    }

    /**
     * install.php faylini yuklab olish uchun route.
     * install.php public/ tashqarisida — shu sababli faylni o'qib,
     * attachment header bilan yuboramiz (path traversal xavfi yo'q: aniqli fayl).
     */
    public function installDownload()
    {
        $path = base_path('install.php');
        if (!is_file($path)) {
            throw new RouteNotFoundException('install.php');
        }
        $content = file_get_contents($path);

        return Response::make($content, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="install.php"',
            'Content-Length'      => (string) strlen($content),
        ]);
    }
}