<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;

function initialize_firebase() {
    try {
        $factory = (new Factory)
            ->withServiceAccount(__DIR__ . '/sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json')
            ->withDatabaseUri('https://sleep-monitor-3e4c3-default-rtdb.europe-west1.firebasedatabase.app/');
        return $factory->createDatabase();
    } catch (FirebaseException $e) {
        die("Firebase SDK error: " . $e->getMessage());
    }
}

function get_sleep_data_by_week($userId, $week) {
    $database = initialize_firebase();
    $heartbeatRef = $database->getReference("users/$userId/heartbeat_data");

    try {
        $date = new DateTime();
        [$year, $weekNumber] = explode('-W', $week);
        $date->setISODate((int)$year, (int)$weekNumber);

        $weekStart = $date->format('Y-m-d');
        $date->modify('+6 days');
        $weekEnd = $date->format('Y-m-d');

        $data = $heartbeatRef->orderByKey()
            ->startAt($weekStart)
            ->endAt($weekEnd)
            ->getValue();

        return $data ?? [];
    } catch (FirebaseException $e) {
        die("Firebase error: " . $e->getMessage());
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
