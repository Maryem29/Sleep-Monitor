<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testAboutUsPage()
    {
        $response = $this->get('/about-us.html');
        $this->assertStringContainsString('About Sleep Monitor', $response);
    }
}