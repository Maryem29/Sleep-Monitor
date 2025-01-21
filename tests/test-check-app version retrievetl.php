<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testAppVersion()
    {
        $response = $this->get('/app-information.php');
        $this->assertEquals('1.0.0', $response['version']);
    }
}