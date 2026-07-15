<?php
namespace Qadamchi\Testing;

use Qadamchi\Http\Request;
use Qadamchi\Routing\Route;
use Qadamchi\Testing\TestResponse;

/**
 * Mini test bazasi (PHPUnit'siz). `php qadamchi test` orqali ishga tushadi.
 *   class ExampleTest extends \Qadamchi\Testing\TestCase {
 *       public function test_home(): void { $this->assertEquals(200, $this->get('/')->status()); }
 *   }
 */
abstract class TestCase
{
    protected function setUp(): void {}
    protected function tearDown(): void {}

    protected function get(string $uri, array $server = []): TestResponse
    {
        return $this->call('GET', $uri, [], $server);
    }

    protected function post(string $uri, array $data = [], array $server = []): TestResponse
    {
        return $this->call('POST', $uri, $data, $server);
    }

    protected function call(string $method, string $uri, array $data = [], array $server = []): TestResponse
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        if ($method === 'GET') {
            $_GET = $data; $_POST = [];
        } else {
            $_POST = $data; $_GET = [];
        }
        $_SERVER = array_merge($_SERVER, $server);

        // Request'ni qayta yaratamiz — ham instance, ham container'da yangilaymiz
        $newRequest = Request::capture();
        Request::setInstance($newRequest);
        if (class_exists(\Qadamchi\Container\Container::class)) {
            \Qadamchi\Container\Container::getInstance()->instance(Request::class, $newRequest);
        }

        ob_start();
        $status = 200;
        try {
            $response = Route::dispatch(Request::instance());
            // Statusni to'g'ridan-to'g'ri Response obyektidan olamiz —
            // headers_sent bo'lganda ham to'g'ri (masalan, redirect 302).
            $status = $response->getStatusCode();
            $response->send();
        } catch (\Throwable $e) {
            // Handler'ga topshiramiz — u http_response_code ni to'g'rilaydi va error sahifasini chiqaradi.
            if (class_exists(\Qadamchi\Exceptions\Handler::class)) {
                \Qadamchi\Exceptions\Handler::handleException($e);
            }
            $status = http_response_code() ?: 500;
        }
        $output = ob_get_clean();

        return new TestResponse($output, $status);
    }

    // Oddiy assertionlar
    public function assertEquals($expected, $actual, string $msg = ''): void
    {
        if ($expected !== $actual) $this->fail($msg ?: "Kutilgan " . print_r($expected, true) . " != " . print_r($actual, true));
    }
    public function assertTrue($cond, string $msg = ''): void { if (!$cond) $this->fail($msg ?: "True kutilgan edi"); }
    public function assertFalse($cond, string $msg = ''): void { if ($cond) $this->fail($msg ?: "False kutilgan edi"); }
    public function assertCount(int $n, $array, string $msg = ''): void { if (count($array) !== $n) $this->fail($msg); }

    protected function fail(string $msg): void { throw new \AssertionError($msg); }
}