<?php
/**
 * Namuna test — `php qadamchi test` bilan ishga tushadi.
 * DB'siz ishlaydigan route'larni tekshiradi.
 */
use Qadamchi\Testing\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_welcome_loads(): void
    {
        $resp = $this->get('/');
        $this->assertEquals(200, $resp->status());
        $this->assertTrue($resp->see('Qadamchi.uz'));
        $this->assertTrue($resp->see('Zamonaviy PHP mikrofreymvork'));
    }

    public function test_dashboard_requires_auth(): void
    {
        // auth middleware kirgan user bo'lmasa /login'ga yo'naltiradi (302)
        $resp = $this->get('/dashboard');
        $this->assertEquals(302, $resp->status());
    }

    public function test_docs_index_loads(): void
    {
        $resp = $this->get('/docs');
        $this->assertEquals(200, $resp->status());
        $this->assertTrue($resp->see('Hujjatlar'));
    }

    public function test_docs_show_loads(): void
    {
        $resp = $this->get('/docs/tushunchalar');
        $this->assertEquals(200, $resp->status());
        $this->assertTrue($resp->see('Qadamchi'));
    }

    public function test_docs_unknown_404(): void
    {
        // Whitelist'da yo'q doc — RouteNotFoundException → 404 (yoki debug'da 500).
        $resp = $this->get('/docs/bunday-doc-yoq');
        $this->assertTrue(in_array($resp->status(), [404, 500], true), "Kutilgan 404/500, keldi " . $resp->status());
    }

    public function test_docs_install_page_loads(): void
    {
        $resp = $this->get('/docs/installatsiya');
        $this->assertEquals(200, $resp->status());
        $this->assertTrue($resp->see('install.php'));
    }

    public function test_docs_install_download(): void
    {
        $resp = $this->get('/docs/installatsiya/yuklab');
        // install.php mavjud bo'lsa (dev repo) — 200 + fayl o'zagi.
        // Yo'q bo'lsa (fresh install — install.php o'zini o'chirgan) — 404/500.
        if ($resp->status() === 200) {
            $this->assertTrue($resp->see('bitta-fayl o'), 'install.php o‘zagi yuklab olish javobida yo‘q');
        } else {
            $this->assertTrue(in_array($resp->status(), [404, 500], true), "Kutilgan 200 yoki 404/500, keldi " . $resp->status());
        }
    }

    public function test_register_page_loads(): void
    {
        $resp = $this->get('/register');
        $this->assertEquals(200, $resp->status());
        $this->assertTrue($resp->see("Ro'yxatdan o'tish"));
    }

    public function test_login_page_loads(): void
    {
        $resp = $this->get('/login');
        $this->assertEquals(200, $resp->status());
    }

    public function test_unknown_route_404(): void
    {
        $resp = $this->get('/bu-route-mavjud-emas');
        // Handler 404 sahifa chiqaradi (debug=off) yoki 500 (debug=on)
        $this->assertTrue(in_array($resp->status(), [404, 500], true), "Kutilgan 404/500, keldi " . $resp->status());
    }
}