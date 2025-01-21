import h5py
import numpy as np
import random
import datetime

def create_fake_opensignals_h5(filename, num_days=20, measurements_per_day=5):
    """
    Creates a fake OpenSignals-like .h5 file containing heartbeat data 
    for the last `num_days` days from today going backward.

    Args:
        filename: Name of the output .h5 file.
        num_days: Number of days to generate data for (default: 120).
        measurements_per_day: Number of measurements to generate per day.
    """
    
    with h5py.File(filename, 'w') as f:
        # Create datasets in the h5 file
        total_measurements = num_days * measurements_per_day
        dset_timestamps = f.create_dataset('timestamps', (total_measurements,), dtype='S20') 
        dset_heart_rate = f.create_dataset('heart_rate', (total_measurements,), dtype=np.float32)

        # Generate random heart rate data for the last `num_days`
        for i in range(num_days):
            # Calculate the date for `i` days ago
            day_date = datetime.datetime.now() - datetime.timedelta(days=i)
            day_start = datetime.datetime(day_date.year, day_date.month, day_date.day)

            for j in range(measurements_per_day):
                # Generate a random timestamp within the day
                timestamp = day_start + datetime.timedelta(hours=random.randint(0, 23), minutes=random.randint(0, 59))
                dset_timestamps[i * measurements_per_day + j] = timestamp.strftime('%Y-%m-%d %H:%M:%S') 

                # Generate a random heart rate value (between 25 and 140 bpm)
                dset_heart_rate[i * measurements_per_day + j] = random.randint(25, 140)

# Example usage:
create_fake_opensignals_h5('fake_heartbeat_data.h5', num_days=120, measurements_per_day=5)