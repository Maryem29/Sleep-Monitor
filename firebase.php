<?php
// Include the Composer autoloader
require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;

function initialize_firebase_database() {
    echo "Initializing Firebase...\n";

    try {
        $firebase = (new Factory)
            ->withServiceAccount(__DIR__ . '/sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-1619cac80a.json')
            ->withDatabaseUri('https://sleep-monitor-3e4c3-default-rtdb.europe-west1.firebasedatabase.app/'); // Ensure the URI is correct
        
        echo "Firebase initialized successfully!\n";
        return $firebase->createDatabase();
    } catch (Exception $e) {
        // Print detailed error message
        echo "Error initializing Firebase: " . $e->getMessage();
        exit;
    }
}

function write_to_firebase($path, $data) {
    $database = initialize_firebase_database();

    try {
        $database->getReference($path)->push($data);
        echo "Data written to Firebase successfully!";
    } catch (Exception $e) {
        echo "Error writing to Firebase: " . $e->getMessage();
        exit;
    }
}

function get_data_from_firebase($path) {
    $database = initialize_firebase_database();

    try {
        $data = $database->getReference($path)->getValue();
        echo "Data retrieved successfully!";
        return $data;
    } catch (Exception $e) {
        echo "Error retrieving data from Firebase: " . $e->getMessage();
        exit;
    }
}
?>

