<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testInvalidSleepData()
    {
        $response = $this->post('/report.php', [
            'user_id' => 1,
            'sleep_hours' => -5,
            'sleep_quality' => 'N/A',
        ]);
        $this->assertFalse($response['success']);
    }
}