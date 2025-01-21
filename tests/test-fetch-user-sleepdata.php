<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testFetchUserSleepData()
    {
        $this->post('/report.php', [
            'user_id' => 1,
            'sleep_hours' => 7,
            'sleep_quality' => 'Good',
        ]);
        $response = $this->get('/statistics.php?user_id=1');
        $this->assertCount(1, $response['sleep_data']);
    }
}