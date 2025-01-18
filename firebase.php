<?php
// Include the Composer autoloader
require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;

function initialize_firebase_database() {
    echo "Initializing Firebase...\n";

    try {
        // Get the Firebase service account key from the environment variable
        $firebaseKey = getenv('FIREBASE_SERVICE_ACCOUNT_KEY'); // Correct environment variable name

        // Check if the environment variable is not set
        if (!$firebaseKey) {
            throw new Exception("Firebase service account key not found in environment variables.");
        }

        // Decode the JSON string from the environment variable
        $serviceAccount = json_decode($firebaseKey, true);

        // Initialize the Firebase factory with the decoded service account credentials
        $factory = (new Factory)
            ->withServiceAccount($serviceAccount)  // Use the decoded JSON array
            ->withDatabaseUri('https://sleep-monitor-3e4c3-default-rtdb.europe-west1.firebasedatabase.app/'); // Ensure the URI is correct

        echo "Firebase initialized successfully!\n";
        return $factory->createDatabase();
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
