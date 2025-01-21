<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testNoSleepDataLoss()
    {
        $this->post('/report.php', [
            'user_id' => 1,
            'sleep_hours' => 6,
            'sleep_quality' => 'Poor',
        ]);
        $response = $this->get('/statistics.php?user_id=1');
        $this->assertEquals(6, $response['sleep_data'][0]['sleep_hours']);
    }
}