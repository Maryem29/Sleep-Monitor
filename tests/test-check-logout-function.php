<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testLogout()
    {
        $this->get('/logout.php');
        $response = $this->get('/profile.php?user_id=1');
        $this->assertFalse($response['success']);
    }

    private function post($url, $data)
    {
    }

    private function get($url)
    {
    }
}