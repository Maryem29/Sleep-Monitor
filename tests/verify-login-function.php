<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase
{
    public function testLogin()
    {
        $this->post('/register.php', [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'Password123',
        ]);
        $response = $this->post('/profile.php', [
            'email' => 'testuser@example.com',
            'password' => 'Password123',
        ]);
        $this->assertEquals('testuser', $response['username']);
    }
}
