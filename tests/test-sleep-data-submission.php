<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testSubmitSleepData()
    {
        $response = $this->post('/report.php', [
            'user_id' => 1,
            'sleep_hours' => 7,
            'sleep_quality' => 'Good',
        ]);
        $this->assertTrue($response['success']);
    }
}
