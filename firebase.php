<?php

// Include the Composer autoloader
require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;

function initialize_firebase() {
    try {
        // Initialize Firebase with Service Account credentials
        $firebase = (new Factory)
            ->withServiceAccount(__DIR__ . '/sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json')
            ->withDatabaseUri('https://sleep-monitor-3e4c3-default-rtdb.europe-west1.firebasedatabase.app/'); // Realtime Database URI
        return $firebase;
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error initializing Firebase: " . $e->getMessage() . "\n";
        exit;
    }
}

function register_user($userId, $userData) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        $database->getReference('users/' . $userId)->set($userData);
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error registering user: " . $e->getMessage() . "\n";
        exit;
    }
}

function update_user_profile($userId, $profileData) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        $database->getReference('users/' . $userId)->update($profileData);
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error updating user profile: " . $e->getMessage() . "\n";
        exit;
    }
}

function upload_sleep_data($userId, $sleepData) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        $database->getReference('users/' . $userId . '/sleepData')->push($sleepData);
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error uploading sleep data: " . $e->getMessage() . "\n";
        exit;
    }
}

function get_user_data($userId) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        $userSnapshot = $database->getReference('users/' . $userId)->getSnapshot();
        return $userSnapshot->getValue();
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error retrieving user data: " . $e->getMessage() . "\n";
        exit;
    }
}

function get_all_users_data() {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        $snapshot = $database->getReference('users')->getSnapshot();
        return $snapshot->getValue();
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error retrieving all users data: " . $e->getMessage() . "\n";
        exit;
    }
}

function get_sleep_data($userId) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        $snapshot = $database->getReference('users/' . $userId . '/sleepData')->getSnapshot();
        return $snapshot->getValue();
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error retrieving sleep data: " . $e->getMessage() . "\n";
        exit;
    }
}

function get_sleep_data_by_date($userId, $date) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        $snapshot = $database->getReference('users/' . $userId . '/sleepData')
            ->orderByChild('date')
            ->equalTo($date)
            ->getSnapshot();

        return $snapshot->getValue();
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error retrieving sleep data: " . $e->getMessage() . "\n";
        exit;
    }
}

function get_user_sleep_statistics($userId) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        $snapshot = $database->getReference('users/' . $userId . '/sleepData')->getSnapshot();
        $sleepData = $snapshot->getValue();

        if (!$sleepData) {
            return [
                'avgSleepDuration' => 'No data available',
                'sleepEfficiency' => 'No data available',
                'deepSleep' => 'No data available',
                'sleepTrends' => [],
                'recommendations' => ['Try to sleep 7+ hours regularly for better health.']
            ];
        }

        $totalSleepDuration = 0;
        $totalEfficiency = 0;
        $totalDeepSleep = 0;
        $count = 0;
        $sleepTrends = [];
        $recommendations = [];

        foreach ($sleepData as $date => $data) {
            if (isset($data['sleepDuration'], $data['efficiency'], $data['deepSleep'])) {
                $totalSleepDuration += $data['sleepDuration'];
                $totalEfficiency += $data['efficiency'];
                $totalDeepSleep += $data['deepSleep'];
                $sleepTrends[$date] = $data['sleepDuration'];
                $count++;
            }
        }

        $avgSleepDuration = $count > 0 ? round($totalSleepDuration / $count, 2) : 0;
        $avgEfficiency = $count > 0 ? round($totalEfficiency / $count, 2) : 0;
        $avgDeepSleep = $count > 0 ? round($totalDeepSleep / $count, 2) : 0;

        if ($avgSleepDuration < 7) {
            $recommendations[] = "Try to increase your sleep duration to 7+ hours.";
        }
        if ($avgEfficiency < 80) {
            $recommendations[] = "Consider improving your sleep quality for better efficiency.";
        }

        return [
            'avgSleepDuration' => "$avgSleepDuration hours",
            'sleepEfficiency' => "$avgEfficiency%",
            'deepSleep' => "$avgDeepSleep hours",
            'sleepTrends' => $sleepTrends,
            'recommendations' => $recommendations,
        ];
    } catch (FirebaseException $e) {
        return [
            'error' => 'Firebase error: ' . $e->getMessage()
        ];
    } catch (Exception $e) {
        return [
            'error' => 'Error retrieving sleep statistics: ' . $e->getMessage()
        ];
    }
}
