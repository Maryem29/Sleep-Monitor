<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testBulkSleepDataInput()
    {
        $data = [
            ['user_id' => 1, 'sleep_hours' => 6, 'sleep_quality' => 'Good'],
            ['user_id' => 1, 'sleep_hours' => 8, 'sleep_quality' => 'Excellent'],
        ];
        $response = $this->post('/report.php', $data);
        $this->assertTrue($response['success']);
    }
}