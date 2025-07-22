<?php
require_once __DIR__ . '/TestResponse.php';

// Ensure the TestResponse class exists
if (!class_exists('TestResponse')) {
    class TestResponse {
        private $output;

        public function __construct($output) {
            $this->output = $output;
        }

        public function getOutput() {
            return $this->output;
        }
    }
}

abstract class TestCase {
    protected function setUp() {
        // Test database setup
    }
    
    protected function tearDown() {
        // Cleanup
    }
    
    public function get($uri, $data = []) {
        return $this->makeRequest('GET', $uri, $data);
    }
    
    public function post($uri, $data = []) {
        return $this->makeRequest('POST', $uri, $data);
    }
    
    private function makeRequest($method, $uri, $data) {
        // Mock HTTP request
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        
        if ($method === 'POST') {
            $_POST = $data;
        } else {
            $_GET = $data;
        }
        
        ob_start();
        Route::dispatch();
        $output = ob_get_clean();
        
        return new TestResponse($output);
    }
    
    protected function assertEquals($expected, $actual) {
        if ($expected !== $actual) {
            throw new Exception("Expected $expected, got $actual");
        }
    }
    
    protected function assertTrue($condition) {
        if (!$condition) {
            throw new Exception("Condition is not true");
        }
    }
}