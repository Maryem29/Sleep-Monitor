<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase
{
    // Test 1: Verify user registration
    public function testUserRegistration()
    {
        $response = $this->post('/register.php', [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'Password123',
        ]);
        $this->assertTrue($response['success']);
    }
}