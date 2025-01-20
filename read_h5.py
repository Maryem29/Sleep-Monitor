import h5py
import os
import sys
import firebase_admin
from firebase_admin import credentials, db
from datetime import datetime
import numpy as np  # Ensure numpy is imported for np.bytes_ handling

# Initialize Firebase (make sure you have the Firebase service account credentials)
def initialize_firebase():
    cred = credentials.Certificate('sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json')  # Update this path if needed
    firebase_admin.initialize_app(cred, {
        'databaseURL': 'https://sleep-monitor-3e4c3-default-rtdb.europe-west1.firebasedatabase.app/'
    })

def read_and_import_h5_data_to_firebase(user_id):
    """
    Reads .h5 files from the current directory and uploads their data to Firebase for the specified user.
    This function checks if the data already exists before uploading.
    
    Args:
        user_id: Firebase user ID under which to store the data.
    """
    # Get the current working directory (where the script is located)
    folder_path = os.getcwd()

    # List all .h5 files in the current directory
    h5_files = [f for f in os.listdir(folder_path) if f.endswith('.h5')]
    if not h5_files:
        print(f"No .h5 files found in the folder {folder_path}.")
        sys.exit(1)

    # Loop through each .h5 file and import its data
    for file_name in h5_files:
        file_path = os.path.join(folder_path, file_name)
        with h5py.File(file_path, 'r') as f:
            print(f"Reading data from {file_name}...")
            if 'timestamps' in f and 'heart_rate' in f:
                timestamps = f['timestamps'][:]
                heart_rate = f['heart_rate'][:]
                
                # Create a reference to the specific user in Firebase
                user_ref = db.reference(f'users/{user_id}/heartbeat_data')

                # Get existing data to check for duplicates
                existing_data = user_ref.get()  # Fetch all existing data for comparison
                existing_dates = {
                    entry for entry in existing_data.keys()
                    if isinstance(entry, str) and len(entry) == 10  # Look for date format YYYY-MM-DD
                } if existing_data else set()

                # Process each timestamp and heart rate value
                for i in range(len(timestamps)):
                    # Handle if the timestamp is bytes and needs to be decoded
                    if isinstance(timestamps[i], np.bytes_):
                        timestamp_str = timestamps[i].decode('utf-8')  # Decode bytes to string
                    else:
                        timestamp_str = str(timestamps[i])  # Otherwise, convert directly to string
                    
                    try:
                        # Try to parse the timestamp into a datetime object
                        if timestamp_str.isdigit():
                            timestamp_dt = datetime.fromtimestamp(int(timestamp_str))  # Convert Unix timestamp
                        else:
                            timestamp_dt = datetime.strptime(timestamp_str, '%Y-%m-%d %H:%M:%S')  # Adjust format as needed

                        date_str = timestamp_dt.strftime('%Y-%m-%d')
                    except Exception as e:
                        print(f"Error parsing timestamp {timestamp_str}: {e}")
                        continue  # Skip this entry if parsing fails
                    
                    # Create a reference for the specific date in Firebase
                    date_ref = user_ref.child(date_str)

                    # If the date already exists, append the heart rate, otherwise create the list
                    if date_str in existing_dates:
                        # Get existing heart rates for the day
                        existing_heart_rates = date_ref.get()

                        # If it's a dictionary (which Firebase may return), convert it to a list of values
                        if isinstance(existing_heart_rates, dict):
                            existing_heart_rates = list(existing_heart_rates.values())
                        
                        if existing_heart_rates is None:
                            existing_heart_rates = []

                        # Append the new heart rate to the list
                        existing_heart_rates.append(float(heart_rate[i]))  # Append the new heart rate

                        # Save the updated list of heart rates for the date
                        date_ref.set(existing_heart_rates)
                        print(f"Appended heart rate {heart_rate[i]} to date {date_str}.")
                    else:
                        # If the date doesn't exist, create a new list with the current heart rate
                        date_ref.set([float(heart_rate[i])])  # Initialize with the first heart rate
                        print(f"Uploaded heart rate {heart_rate[i]} for date {date_str}.")
            else:
                print(f"Error: Missing required datasets in {file_name}")  # Indented correctly under 'if' block


def import_h5_data_to_firebase(user_id):
    """
    Wrapper function to read .h5 data from the folder and upload it to Firebase.
    This function ensures no duplicates are uploaded.
    """
    print(f"Fetching data from the current folder and uploading to Firebase for user {user_id}")
    read_and_import_h5_data_to_firebase(user_id)
    print("Data successfully uploaded to Firebase.")

if __name__ == "__main__":
    # Ensure the user ID is provided as a command-line argument
    if len(sys.argv) != 2:
        print("Usage: python3 import_h5_to_firebase.py <user_id>")
        sys.exit(1)

    user_id = sys.argv[1]  # This should be the logged-in user's ID in Firebase

    # Initialize Firebase
    initialize_firebase()
    
    # Import .h5 data and upload to Firebase
    import_h5_data_to_firebase(user_id)