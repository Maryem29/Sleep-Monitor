<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
        public function testInvalidLogin()
    {
        $response = $this->post('/profile.php', [
            'email' => 'wronguser@example.com',
            'password' => 'WrongPassword',
        ]);
        $this->assertFalse($response['success']);
    }
}