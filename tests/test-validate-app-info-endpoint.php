<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testAppInformation()
    {
        $response = $this->get('/app-information.php');
        $this->assertArrayHasKey('version', $response);
    }
}