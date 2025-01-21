<?php

use PHPUnit\Framework\TestCase;

class SleepMonitorTests extends TestCase

{
    public function testUnauthorizedDataAccess()
    {
        $response = $this->get('/statistics.php?user_id=999');
        $this->assertFalse($response['success']);
    }
}