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

    /**
     * Versiya — bitta markaziy manba (Version::VERSION) + config/app.php wiring.
     */
    public function test_version_is_3(): void
    {
        $this->assertEquals('3.1.0', \Qadamchi\Support\Version::VERSION);
        $this->assertEquals('3.1.0', config('app.version'));
    }

    /**
     * SQLite grammar — Blueprint tomonidan generatsiya qilingan DDL haqiqiy
     * SQLite'da xatosiz bajarilishi va unique index ishlashi.
     * (pdo_sqlite yo'q bo'lsa — sokin o'tkazib yuboriladi.)
     */
    public function test_sqlite_grammar_executes(): void
    {
        if (!extension_loaded('pdo_sqlite')) return; // sqlite yo'q — skip

        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $grammar = new \Qadamchi\Database\Grammars\SQLiteGrammar();
        $blueprint = new \Qadamchi\Database\Blueprint('users', $grammar);
        $blueprint->id();
        $blueprint->string('name');
        $blueprint->string('email')->unique();
        $blueprint->string('password');
        $blueprint->timestamps();

        // Har bir statement (CREATE TABLE + CREATE UNIQUE INDEX) xatosiz bajarilishi kerak.
        foreach ($blueprint->toCreateStatements() as $sql) {
            $pdo->exec($sql);
        }

        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")
                      ->fetchAll(\PDO::FETCH_COLUMN);
        $this->assertTrue(in_array('users', $tables, true), 'users jadvali sqlite\'da yaratilmadi');

        $indexes = $pdo->query("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name='users'")
                       ->fetchAll(\PDO::FETCH_COLUMN);
        $this->assertTrue(in_array('users_email_unique', $indexes, true), 'unique index sqlite\'da yaratilmadi');

        // insert + duplicate unique xato berishi kerak.
        $now = date('Y-m-d H:i:s');
        $pdo->exec("INSERT INTO users (name,email,password,created_at,updated_at) VALUES ('A','a@b.uz','x','$now','$now')");
        $duplicateRejected = false;
        try {
            $pdo->exec("INSERT INTO users (name,email,password,created_at,updated_at) VALUES ('B','a@b.uz','x','$now','$now')");
        } catch (\Throwable $e) {
            $duplicateRejected = true;
        }
        $this->assertTrue($duplicateRejected, 'unique constraint sqlite\'da ishlamadi');
    }
}