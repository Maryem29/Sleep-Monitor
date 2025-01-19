<?php

// Include the Composer autoloader
require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

function initialize_firebase() {
    // Removed the echo statement that caused output before headers
    try {
        // Initialize Firebase with Service Account credentials
        $firebase = (new Factory)
            ->withServiceAccount(__DIR__ . '/sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json')
            ->withDatabaseUri('https://sleep-monitor-3e4c3-default-rtdb.europe-west1.firebasedatabase.app/'); // Realtime Database URI

        return $firebase;
    } catch (Exception $e) {
        // Print detailed error message
        echo "Error initializing Firebase: " . $e->getMessage();
        exit;
    }
}
// Function to register a new user and save their profile in Realtime Database
function register_user($userId, $userData) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        // Save user data to Realtime Database under 'users' node
        $database->getReference('users/' . $userId)->set($userData);
        echo "User registered successfully!\n";
    } catch (Exception $e) {
        echo "Error registering user: " . $e->getMessage();
        exit;
    }
}

// Function to update the user's profile data
function update_user_profile($userId, $profileData) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        // Update user's profile data in the Realtime Database
        $database->getReference('users/' . $userId)->update($profileData);
        echo "User profile updated successfully!\n";
    } catch (Exception $e) {
        echo "Error updating user profile: " . $e->getMessage();
        exit;
    }
}

// Function to upload sleep data for the user
function upload_sleep_data($userId, $sleepData, $sleepDate) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        // Use 'set()' to upload data under a specific date without generating a new unique ID
        $database->getReference('users/' . $userId . '/sleepData/' . $sleepDate)->set($sleepData);
        echo "Sleep data uploaded successfully!\n";
    } catch (Exception $e) {
        echo "Error uploading sleep data: " . $e->getMessage();
        exit;
    }
}




function get_sleep_data_by_date($userId, $sleepDate) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        $snapshot = $database->getReference("users/{$userId}/sleepData/{$sleepDate}")->getSnapshot();
        $sleepData = $snapshot->getValue();

        echo "Data retrieved for {$sleepDate}: ";
        print_r($sleepData);  // Check what is being returned
        
        if ($sleepData) {
            echo "Sleep data for {$sleepDate} retrieved successfully!\n";
            return $sleepData;
        } else {
            echo "No sleep data found for {$sleepDate}.\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error retrieving sleep data: " . $e->getMessage();
        exit;
    }
}






// Function to retrieve all user data from Realtime Database
function get_all_users_data() {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        // Retrieve all users from the 'users' node
        $snapshot = $database->getReference('users')->getSnapshot();
        $usersData = $snapshot->getValue();
        echo "All users data retrieved successfully!\n";
        return $usersData;
    } catch (Exception $e) {
        echo "Error retrieving all users data: " . $e->getMessage();
        exit;
    }
}




// Function to retrieve all user data (including username, surname, email, age, gender, proficiency)
function get_user_data($userId) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        // Retrieve user data from Firebase
        $userSnapshot = $database->getReference('users/' . $userId)->getSnapshot();
        $userData = $userSnapshot->getValue();

        echo "User data retrieved successfully!\n";
        return $userData;
    } catch (Exception $e) {
        echo "Error retrieving user data: " . $e->getMessage();
        exit;
    }
}


// Function to retrieve sleep data for the user
function get_sleep_data($userId) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        // Retrieve sleep data for the user
        $snapshot = $database->getReference('users/' . $userId . '/sleepData')->getSnapshot();
        $sleepData = $snapshot->getValue();
        echo "Sleep data retrieved successfully!\n";
        return $sleepData;
    } catch (Exception $e) {
        echo "Error retrieving sleep data: " . $e->getMessage();
        exit;
    }
}




// firebase_sleep_data.php

function get_user_sleep_data($userId) {
    global $database;

    $date_today = date('Y-m-d');
    $path = 'sleep_data/'.$userId.'/'.$date_today; // Assuming data is stored by date and user ID
    $snapshot = $database->getReference($path)->getValue();

    if (empty($snapshot)) {
        return null; // No data found for today
    }

    return $snapshot; // Return the fetched data
}






// Assuming we have heart rate and movement data
function process_sleep_data($data) {
    $total_sleep_time = 0;
    $awake_time = 0;
    $sleep_stages = ['Light' => 0, 'Deep' => 0, 'REM' => 0];
    
    // Process each sleep session
    foreach ($data as $session) {
        $start_time = strtotime($session['start_time']);
        $end_time = strtotime($session['end_time']);
        
        $sleep_duration = ($end_time - $start_time) / 3600; // Sleep time in hours
        $total_sleep_time += $sleep_duration;

        // Detect sleep stage based on heart rate and movement
        if ($session['heart_rate'] < 50 && $session['movement'] < 0.2) {
            $sleep_stages['Deep'] += $sleep_duration;
        } elseif ($session['heart_rate'] < 60 && $session['movement'] < 0.5) {
            $sleep_stages['Light'] += $sleep_duration;
        } else {
            $sleep_stages['REM'] += $sleep_duration;
        }
    }

    $awake_time = 24 - $total_sleep_time; // Deduct sleep time from 24 hours
    return ['total_sleep_time' => $total_sleep_time, 'awake_time' => $awake_time, 'sleep_stages' => $sleep_stages];
}


?>
