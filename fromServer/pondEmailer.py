import pymysql
import paho.mqtt.client as mqtt
import json
import datetime
import pytz
import time
import subprocess
        
def get_db_connection():
    return pymysql.connect(
        host='localhost',
        user='root',
        password='ICPHpass!',
        database='pond_monitoring',
        autocommit=True   # Important!
    )

conn = get_db_connection()
cursor = conn.cursor()

def reconnect_db():
    global conn, cursor
    try:
        conn.ping(reconnect=True)
        cursor = conn.cursor()
    except:
        print("Reconnecting to database...")
        conn = get_db_connection()
        cursor = conn.cursor()

def send_email(pyTOphp):
    # print(pyTOphp)
    """Trigger PHP script to send an email notification."""
    # php_exe_path = r"C:\xampp\php\php.exe"  # For Windows XAMPP
    # php_script_path = r"C:\xampp\htdocs\PondMonitoringSystem\dashboard\send_email.php"
    
    php_exe_path = "/usr/bin/php" 
    php_script_path = "/var/www/icph/PondMonitoringSystem/dashboard/send_email.php"

    data_to_pass = json.dumps(pyTOphp)
    process = subprocess.Popen([php_exe_path, php_script_path], stdin=subprocess.PIPE)
    process.communicate(input=data_to_pass.encode())
    print("EMAIL SENT")

def on_connect(client, userdata, flags, rc):
    print(f"Connected with result code {rc}")
    client.subscribe("POND/EmailNotification")

def on_disconnect(client, userdata, rc):
    print(f"Disconnected from MQTT broker with result code {rc}. Attempting to reconnect...")
    while True:
        try:
            client.reconnect()
            print("Reconnected successfully.")
            break
        except Exception as e:
            print(f"Reconnection failed: {e}. Retrying in 5 seconds...")
            time.sleep(5)

def on_message(client, userdata, message):
    print(f"Message received on topic: {message.topic}")
    msg_main = message.payload.decode("utf-8")
    print(f"Received message on topic {message.topic}: {msg_main}")
    
    if message.topic.startswith("POND/EmailNotification"):
        print("Send Email")

        # Convert JSON string to Python dict
        try:
            data = json.loads(msg_main)  
        except json.JSONDecodeError as e:
            print(f"Invalid JSON received: {e}")
            return

        # Get current timestamp in Asia/Singapore timezone
        gmt8_time = datetime.datetime.now(pytz.timezone('Asia/Singapore'))
        timestamp = gmt8_time.strftime('%Y-%m-%d %H:%M:%S')

        # Add timestamp to the dictionary
        data["timestamp"] = timestamp  

        # Send updated data to PHP
        process_data(data)
        send_email(data)
        client.publish("POND/EmailNotification", payload=None, retain=True)

def process_data(data):
    try:
        # data is already a dict, no need to json.loads again
        sensor = data["sensor"]
        value = float(data["value"])
        status = data["status"]
        timestamp = data["timestamp"]

        insert_data(sensor, value, status, timestamp)

    except json.JSONDecodeError as e:
        print(f"Failed to decode JSON: {e}")
    except KeyError as e:
        print(f"Missing key in JSON data: {e}")
    except TypeError as e:
        print(f"Unexpected data type: {e}")
    except Exception as e:
        print(f"An unexpected error occurred: {e}")

def insert_data(sensor, value, status, timestamp):
    reconnect_db()

    try:
        insert_query = f"INSERT INTO `threshold_notif` (sensor, value, status, timestamp) VALUES (%s, %s, %s, %s)"
        cursor.execute(insert_query, (sensor, value, status, timestamp))
        conn.commit()
        print(f"Insert threshold notification Success")
    except Exception as e:
        print("An error occurred:", e)
        conn.rollback()

client = mqtt.Client()
client.on_connect = on_connect
client.on_disconnect = on_disconnect
client.on_message = on_message

username = "mqtt"
password = "ICPHmqtt!"
client.username_pw_set(username, password)

try:
    client.connect("13.214.212.87", 1883, keepalive=60)
except Exception as e:
    print(f"Failed to connect to MQTT broker: {e}")
    exit(1)

client.loop_forever()