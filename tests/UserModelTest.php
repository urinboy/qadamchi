<?php

use App\Models\User;
use App\Requests\CreateUserRequest;

require_once 'core/Testing/TestCase.php';

class UserModelTest extends TestCase {
    public function test_user_creation() {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        $this->assertTrue($user->id > 0);
        $this->assertEquals('Test User', $user->name);
    }
    
    public function test_user_validation() {
        try {
            CreateUserRequest::validate([
                'name' => '',
                'email' => 'invalid-email'
            ]);
            $this->assertTrue(false, 'Validation should have failed');
        } catch (ValidationException $e) {
            $this->assertTrue(true);
        }
    }
}