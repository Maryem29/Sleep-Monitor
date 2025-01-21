<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testFetchProfileData()
    {
        $response = $this->post('/register.php', [
            'username' => 'user123',
            'email' => 'user123@example.com',
            'password' => '123',
        ]);
        $this->assertFalse($response['success']);
    }
}