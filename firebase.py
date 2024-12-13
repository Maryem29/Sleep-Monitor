import firebase_admin
from firebase_admin import credentials, db
import json
from datetime import datetime

def initialize_firebase():
    cred = credentials.Certificate("./firebase_key.json")
    firebase_admin.initialize_app(cred, {
        "databaseURL": "https://sleep-monitor-3e4c3-default-rtdb.europe-west1.firebasedatabase.app"
    })

def write_to_firebase(path, data):
    def serialize(obj):
        if isinstance(obj, datetime):
            return obj.isoformat()
        raise TypeError(f"Type {type(obj)} not serializable")
    
    serialized_data = json.loads(json.dumps(data, default=serialize))
    
    ref = db.reference(path)
    ref.push(serialized_data)