<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testLogoAvailability()
    {
        $response = $this->get('/sleep.png');
        $this->assertNotEmpty($response);
    }
}