<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testSessionPersistence()
    {
        $this->post('/profile.php', [
            'email' => 'testuser@example.com',
            'password' => 'Password123',
        ]);
        $response = $this->get('/profile.php?user_id=1');
        $this->assertEquals('testuser', $response['username']);
    }
}