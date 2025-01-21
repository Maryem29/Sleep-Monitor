<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase
{
    // Test 2: Verify duplicate registration handling
    public function testDuplicateRegistration()
    {
        $this->post('/register.php', [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'Password123',
        ]);
        $response = $this->post('/register.php', [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'Password123',
        ]);
        $this->assertFalse($response['success']);
    }
}