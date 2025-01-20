import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
from scipy.signal import butter, filtfilt

# Function to apply a bandpass filter
def bandpass_filter(data, lowcut, highcut, fs, order=4):
    nyquist = 0.5 * fs
    low = lowcut / nyquist
    high = highcut / nyquist
    b, a = butter(order, [low, high], btype='band')
    filtered_data = filtfilt(b, a, data)
    return filtered_data

# Load the EEG data from the .txt file
file_path = 'C:/Users/79163/Documents/OpenSignals (r)evolution/files/opensignals_98d3c1fd307b_2024-12-12_22-10-22.txt'
with open(file_path, 'r') as file:
    lines = file.readlines()

# Extract the header information
sampling_rate = 100  # Update based on the file header

# Extract data
data_start_index = lines.index("# EndOfHeader\n") + 1
data = pd.DataFrame([list(map(float, line.split())) for line in lines[data_start_index:]])

# Select the EEG channel (A4, last column in this case)
eeg_data = data.iloc[:, -1].values

# Filter the EEG data
lowcut = 0.5  # Lower bound frequency (Hz)
highcut = 45.0  # Upper bound frequency (Hz)
filtered_eeg = bandpass_filter(eeg_data, lowcut, highcut, sampling_rate)

# Plot the raw and filtered EEG data
plt.figure(figsize=(14, 8))

# Plot raw EEG
plt.subplot(2, 1, 1)
plt.plot(eeg_data, color='blue', alpha=0.7)
plt.title("Raw EEG Data")
plt.xlabel("Sample")
plt.ylabel("Amplitude")

# Plot filtered EEG
plt.subplot(2, 1, 2)
plt.plot(filtered_eeg, color='green', alpha=0.7)
plt.title("Filtered EEG Data (0.5-45 Hz)")
plt.xlabel("Sample")
plt.ylabel("Amplitude")

plt.tight_layout()
plt.show()
