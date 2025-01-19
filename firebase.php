<?php

// Include the Composer autoloader
require 'vendor/autoload.php';

use Kreait\Firebase\Factory;

function initialize_firebase() {
    echo "Initializing Firebase...\n";

    try {
        // Initialize Firebase with Service Account credentials
        $firebase = (new Factory)
            ->withServiceAccount(__DIR__ . '/sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json')
            ->withDatabaseUri('https://sleep-monitor-3e4c3-default-rtdb.europe-west1.firebasedatabase.app/'); // Realtime Database URI

        echo "Firebase initialized successfully!\n";
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
function upload_sleep_data($userId, $sleepData) {
    $firebase = initialize_firebase();
    $database = $firebase->createDatabase();

    try {
        // Add sleep data to a subcollection under the user's node
        $database->getReference('users/' . $userId . '/sleepData')->push($sleepData);
        echo "Sleep data uploaded successfully!\n";
    } catch (Exception $e) {
        echo "Error uploading sleep data: " . $e->getMessage();
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
?>
