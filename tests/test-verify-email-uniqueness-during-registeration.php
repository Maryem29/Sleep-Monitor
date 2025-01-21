<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testEmailUniqueness()
    {
        $this->post('/register.php', [
            'username' => 'user1',
            'email' => 'duplicate@example.com',
            'password' => 'Password123',
        ]);
        $response = $this->post('/register.php', [
            'username' => 'user2',
            'email' => 'duplicate@example.com',
            'password' => 'Password123',
        ]);
        $this->assertFalse($response['success']);
    }
}