from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field
from typing import List, Optional
from datetime import datetime
from firebase import initialize_firebase, write_to_firebase
import firebase_admin
from firebase_admin import credentials, db

# Initialize Firebase
initialize_firebase()

app = FastAPI()

class DataFromSensor(BaseModel):
    timestamp: datetime
    sleep_stage: str
    heart_rate: Optional[int] = Field(None, ge=30, le=200)
    brain_activity: float

@app.post("/api/sleep-data/")
async def receive_data_from_sensor(data: DataFromSensor):
    try:
        # Convert incoming data to dictionary and write to Firebase
        data_dict = data.dict()
        write_to_firebase("data_from_the_sensor", data_dict)  # This writes the data to Firebase
        return {"message": "Data has been successfully stored in Firebase", "data": data_dict}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to store data: {str(e)}")

@app.get("/test-firebase")
def test_firebase():
    try:
        # Simple test to ensure Firebase is working
        write_to_firebase("test-path", {"message": "Hello Firebase!"})
        return {"status": "success", "message": "Data written to Firebase"}
    except Exception as e:
        return {"status": "error", "message": str(e)}

@app.post("/process-sleep-data/")
async def process_sleep_data(data: List[DataFromSensor]):
    if not data:
        raise HTTPException(status_code=400, detail="Input data cannot be empty.")
    
    try:
        # Aggregation metrics
        total_time = len(data)  # Assuming each entry represents a unit of time (e.g., 1 minute)
        stage_counts = {"REM": 0, "deep": 0, "light": 0, "awake": 0}
        total_heart_rate = 0
        heart_rate_count = 0
        
        # Process each record
        for entry in data:
            if entry.sleep_stage in stage_counts:
                stage_counts[entry.sleep_stage] += 1
            
            if entry.heart_rate:
                total_heart_rate += entry.heart_rate
                heart_rate_count += 1
        
        # Calculate metrics
        rem_percentage = (stage_counts["REM"] / total_time) * 100
        avg_heart_rate = total_heart_rate / heart_rate_count if heart_rate_count > 0 else None
        
        return {
            "total_records": total_time,
            "sleep_stage_distribution": stage_counts,
            "REM_percentage": rem_percentage,
            "average_heart_rate": avg_heart_rate,
        }
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"An error occurred while processing: {str(e)}")

@app.get("/api/sleep-data/")
async def fetch_sleep_data():
    try:
        # Check Firebase initialization first
        if not firebase_admin._apps:
            raise HTTPException(status_code=500, detail="Firebase not initialized correctly.")
        
        # Fetch data from Firebase
        ref = db.reference("data_from_the_sensor")
        data = ref.get()  # Retrieve all data under the "data_from_the_sensor" path

        if not data:
            return {"message": "No data found", "data": []}

        # Convert Firebase dict to a list
        formatted_data = [value for key, value in data.items()]
        return {"message": "Data fetched successfully", "data": formatted_data}
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to fetch data: {str(e)}")