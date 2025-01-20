import h5py
import numpy as np
import random
import datetime
import sys

def create_fake_opensignals_h5(filename, num_days=30, measurements_per_day=5):
  """
  Creates a fake OpenSignals-like .h5 file containing heartbeat data 
  collected multiple times a day for a given number of days.

  Args:
    filename: Name of the output .h5 file.
    num_days: Number of days for which to generate data.
    measurements_per_day: Number of measurements to generate per day.
  """

  with h5py.File(filename, 'w') as f:
    # Create datasets in the h5 file
    dset_timestamps = f.create_dataset('timestamps', (num_days * measurements_per_day,), dtype='S20') 
    dset_heart_rate = f.create_dataset('heart_rate', (num_days * measurements_per_day,), dtype=np.float32)

    # Generate random heart rate data
    for i in range(num_days):
      for j in range(measurements_per_day):
        # Generate a random timestamp within the day
        day_start = datetime.datetime(year=2024, month=1, day=i+1) 
        timestamp = day_start + datetime.timedelta(hours=random.randint(0, 23), minutes=random.randint(0, 59))
        dset_timestamps[i * measurements_per_day + j] = timestamp.strftime('%Y-%m-%d %H:%M:%S') 

        # Generate a random heart rate value (between 60 and 100 bpm)
        dset_heart_rate[i * measurements_per_day + j] = random.randint(60, 100)



def import_fake_h5(output_file):
    """
    Wrapper function to generate the fake .h5 file.
    """
    print(f"Generating data in {output_file}...")
    create_fake_opensignals_h5(output_file)
    print(f"Data successfully written to {output_file}")

if __name__ == "__main__":
    # Ensure the filename is provided as a command-line argument
    if len(sys.argv) != 2:
        print("Usage: python3 import_fake_h5.py <output_file.h5>")
        sys.exit(1)

    output_file = sys.argv[1]
    import_fake_h5(output_file)