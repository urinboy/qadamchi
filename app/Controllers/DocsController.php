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
    /** Ruxsat etilgan hujjatlar: fayl nomi (kengaytmasiz) => sarlavha. */
    private array $docs = [
        'tushunchalar'      => "Tushunchalar bo'yicha qo'llanma",
        'laravel-otish'     => "Qadamchi → Laravel o'tish",
        'a-b-otish'         => "A → B versiyasiga o'tish",
        'qadamchi-commands' => "Qadamchi CLI buyruqlari",
        'qadamchi_cli'      => "Qadamchi CLI qo'llanma",
        'qadamchi_v2.1'     => "Qadamchi v2.1 strukturasi",
        'installatsiya'     => "O'rnatish — install.php",
    ];

    /** Hujjatlar ro'yxati (TOC). */
    public function index()
    {
        return view('docs.index', ['docs' => $this->docs]);
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

        return view('docs.show', [
            'html'    => $html,
            'name'    => $name,
            'current' => $name,
            'title'   => $this->docs[$name],
            'docs'    => $this->docs,
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