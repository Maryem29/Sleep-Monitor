<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;

putenv("PYTHONIOENCODING=utf-8");
putenv("LANG=en_US.UTF-8");
putenv("LC_ALL=en_US.UTF-8");

$pythonScript = escapeshellcmd("$pythonPath $pythonScriptPath $userId");
$output = shell_exec($pythonScript . " 2>&1");

$jsonContent = file_get_contents(__DIR__ . '/sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json');
if ($jsonContent === false) {
    die('Error reading JSON file.');
}

$decoded = json_decode($jsonContent, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die('JSON parse error: ' . json_last_error_msg());
} else {
    echo 'JSON parsed successfully!';
}

function initialize_firebase() {
    try {
        $factory = (new Factory)
            ->withServiceAccount(__DIR__ . '/sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json')
            ->withDatabaseUri('https://sleep-monitor-3e4c3-default-rtdb.europe-west1.firebasedatabase.app/');
        return $factory->createDatabase(); // Correct method
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

function register_user($userId, $userData) {
    $database = initialize_firebase();

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
    $database = initialize_firebase();

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
    $database = initialize_firebase();

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

function get_all_users_data() {
    $database = initialize_firebase();

    try {
        $snapshot = $database->getReference('users')->getSnapshot();
        $usersData = $snapshot->getValue();
        return $usersData;
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error retrieving all users data: " . $e->getMessage() . "\n";
        exit;
    }
}

function get_user_data($userId) {
    $database = initialize_firebase();

    try {
        $userSnapshot = $database->getReference('users/' . $userId)->getSnapshot();
        $userData = $userSnapshot->getValue();
        return $userData;
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error retrieving user data: " . $e->getMessage() . "\n";
        exit;
    }
}

function get_sleep_data($userId) {
    $database = initialize_firebase();

    try {
        $snapshot = $database->getReference('users/' . $userId . '/sleepData')->getSnapshot();
        $sleepData = $snapshot->getValue();
        return $sleepData;
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error retrieving sleep data: " . $e->getMessage() . "\n";
        exit;
    }
}

function get_sleep_data_by_date($userId, $date) {
    $database = initialize_firebase();

    try {
        $snapshot = $database->getReference('users/' . $userId . '/sleepData')
            ->orderByChild('date')
            ->equalTo($date)
            ->getSnapshot();

        $sleepData = $snapshot->getValue();
        return $sleepData;
    } catch (FirebaseException $e) {
        echo "Firebase SDK error: " . $e->getMessage() . "\n";
        exit;
    } catch (Exception $e) {
        echo "Error retrieving sleep data: " . $e->getMessage() . "\n";
        exit;
    }
}
function get_sleep_quality_data($userId) {
    $firebase = initialize_firebase();
    $database = $firebase; // No need for `createDatabase()` as it’s already returned in `initialize_firebase`

    try {
        // Reference to the sleep quality data in Firebase
        $sleepQualityRef = $database->getReference("users/$userId/sleep_quality");

        // Fetch the data
        $data = $sleepQualityRef->getValue();

        // Return the data or an empty array if no data exists
        return $data ?? [];
    } catch (FirebaseException $e) {
        die("Firebase SDK error: " . $e->getMessage());
    } catch (Exception $e) {
        die("Error retrieving sleep quality data: " . $e->getMessage());
    }
}
function calculate_sleep_quality_from_heartbeat($userId) {
    $database = initialize_firebase();
    $heartbeatRef = $database->getReference("users/$userId/heartbeat_data");

    try {
        $data = $heartbeatRef->getValue();
        $sleepQuality = [];

        // Loop through each date in heartbeat_data
        foreach ($data as $date => $values) {
            if (isset($values['night']) && is_array($values['night'])) {
                $nightData = $values['night'];
                $averageHeartRate = array_sum($nightData) / count($nightData);

                // Determine sleep quality
                if ($averageHeartRate < 60) {
                    $quality = 100; // Good sleep
                } elseif ($averageHeartRate <= 75) {
                    $quality = 70; // Normal sleep
                } else {
                    $quality = 30; // Bad sleep
                }

                // Add to sleep quality data
                $sleepQuality[$date] = $quality;
            }
        }

        return $sleepQuality;
    } catch (FirebaseException $e) {
        die("Firebase error: " . $e->getMessage());
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
function calculate_sleep_quality_last_month($userId) {
    $firebase = initialize_firebase();
    $database = $firebase->getReference("users/$userId/heartbeat_data");

    try {
        $date = new DateTime();
        $date->modify('-1 month'); // Get the date one month ago
        $startDate = $date->format('Y-m-d');

        $today = new DateTime();
        $endDate = $today->format('Y-m-d');

        // Retrieve data for the last month
        $data = $database->orderByKey()
            ->startAt($startDate)
            ->endAt($endDate)
            ->getValue();

        $processedData = [];
        foreach ($data as $day => $values) {
            $dayAverage = !empty($values['night']) ? array_sum($values['night']) / count($values['night']) : 0;
            $sleepQuality = 100 - min(100, max(0, $dayAverage - 60)); // Example quality calculation
            $processedData[$day] = $sleepQuality;
        }

        return $processedData;
    } catch (FirebaseException $e) {
        die("Firebase error: " . $e->getMessage());
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

function calculate_weekly_averages($monthlyData) {
    $weeklyAverages = [];
    $currentWeek = 1;
    $weeklyData = [];

    foreach ($monthlyData as $date => $quality) {
        $dayOfWeek = (new DateTime($date))->format('N'); // 1 (Monday) to 7 (Sunday)

        // Group data by week
        $weeklyData[$currentWeek][] = $quality;

        // If it's Sunday, move to the next week
        if ($dayOfWeek == 7) {
            $weeklyAverages[$currentWeek] = $weeklyData[$currentWeek];
            $currentWeek++;
        }
    }

    // Ensure the last week is added
    if (!empty($weeklyData[$currentWeek])) {
        $weeklyAverages[$currentWeek] = $weeklyData[$currentWeek];
    }

    return $weeklyAverages;
}
function get_sleep_data_by_month($userId, $month) {
    $database = initialize_firebase();
    $heartbeatRef = $database->getReference("users/$userId/heartbeat_data");

    try {
        // Determine the start and end dates of the selected month
        $startDate = new DateTime("$month-01");
        $endDate = clone $startDate;
        $endDate->modify('last day of this month');

        $data = $heartbeatRef->orderByKey()
            ->startAt($startDate->format('Y-m-d'))
            ->endAt($endDate->format('Y-m-d'))
            ->getValue();

        return $data ?? [];
    } catch (FirebaseException $e) {
        die("Firebase error: " . $e->getMessage());
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
function get_all_sleep_data($userId) {
    $firebase = initialize_firebase();
    $database = $firebase->getReference("users/$userId/heartbeat_data");

    try {
        // Retrieve all sleep data for the user
        $data = $database->getValue();
        return $data ?? [];
    } catch (FirebaseException $e) {
        die("Firebase SDK error: " . $e->getMessage());
    } catch (Exception $e) {
        die("Error retrieving all sleep data: " . $e->getMessage());
    }
}

?>