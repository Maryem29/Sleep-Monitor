import h5py
import os
from datetime import datetime
import firebase_admin
from firebase_admin import credentials, db
import sys
import json
import numpy as np

class SleepDataProcessor:
    def __init__(self):
        # Initialize Firebase
        cred = credentials.Certificate('sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json')
        firebase_admin.initialize_app(cred, {
            'databaseURL': 'https://sleep-monitor-3e4c3-default-rtdb.europe-west1.firebasedatabase.app/'
        })
        self.db = db
        
    def process_h5_file(self, file_path):
        """Process a single H5 file and extract sleep metrics"""
        try:
            with h5py.File(file_path, 'r') as f:
                # Extract data from H5 file
                heartbeat = np.mean(f['heartbeat'][()])
                hours_of_sleep = np.sum(f['hours_of_sleep'][()])
                movement = np.mean(f['movement'][()])
                sleep_quality = np.mean(f['sleep_quality'][()])
                
                return {
                    'heartbeat': float(heartbeat),
                    'hours_of_sleep': float(hours_of_sleep),
                    'movement': float(movement),
                    'sleep_quality': float(sleep_quality),
                    'timestamp': datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                }
        except Exception as e:
            print(f"Error processing file {file_path}: {e}")
            return None

    def upload_to_firebase(self, user_id, date, data):
        """Upload processed data to Firebase"""
        try:
            ref = self.db.reference(f'users/{user_id}/sleep_data/{date}')
            ref.set(data)
            return True
        except Exception as e:
            print(f"Error uploading to Firebase: {e}")
            return False

    def process_directory(self, directory_path, user_id, date=None):
        """Process all H5 files in directory for a specific date"""
        if date is None:
            date = datetime.now().strftime('%Y-%m-%d')
            
        all_data = []
        
        # Process all H5 files in directory
        for filename in os.listdir(directory_path):
            if filename.endswith('.h5'):
                file_path = os.path.join(directory_path, filename)
                data = self.process_h5_file(file_path)
                if data:
                    all_data.append(data)
        
        if all_data:
            # Calculate daily averages
            daily_summary = {
                'heartbeat': np.mean([d['heartbeat'] for d in all_data]),
                'hours_of_sleep': np.sum([d['hours_of_sleep'] for d in all_data]),
                'movement': np.mean([d['movement'] for d in all_data]),
                'sleep_quality': np.mean([d['sleep_quality'] for d in all_data]),
                'number_of_readings': len(all_data),
                'raw_data': all_data
            }
            
            # Upload to Firebase
            success = self.upload_to_firebase(user_id, date, daily_summary)
            
            if success:
                return daily_summary
        
        return None

def main():
    if len(sys.argv) < 2:
        print("Usage: python extract_data.py <user_id> [date]")
        sys.exit(1)
        
    user_id = sys.argv[1]
    date = sys.argv[2] if len(sys.argv) > 2 else None
    
    processor = SleepDataProcessor()
    directory_path = "fake_h5_data"  # Change this to your H5 files directory
    
    result = processor.process_directory(directory_path, user_id, date)
    
    if result:
        # Print JSON output for PHP to capture
        print(json.dumps(result))
    else:
        print(json.dumps({"error": "No data processed"}))

if __name__ == "__main__":
    main()
