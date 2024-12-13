import requests
import random
from datetime import datetime
import time
import logging

API_URL = "http://127.0.0.1:8000/api/sleep-data/"  # Change to deployed URL later

while True:
    # Generate random sensor data
    data = {
        "timestamp": datetime.now().isoformat(),
        "heart_rate": random.randint(60, 100),
        "brain_activity": random.uniform(1.0, 5.0)
    }
    
    # Send data to the API
    response = requests.post(API_URL, json=data)
    print(f"Sent: {data}, Response: {response.json()}")
    
    time.sleep(1)

logging.basicConfig(level=logging.DEBUG)
data = {"sensor_id": "123", "timestamp": "2023-12-13T16:00:00", "value": 42}
response = requests.post(API_URL, json=data)
logging.debug(f"Response status: {response.status_code}, body: {response.text}")