import paho.mqtt.client as mqtt
import json
import datetime
import pytz
import time
import subprocess
        
def send_email(pyTOphp):\

    """Trigger PHP script to send an email notification."""
    php_exe_path = r"C:\xampp\php\php.exe"  # For Windows XAMPP
    php_script_path = r"C:\xampp\htdocs\PondMonitoringSystem\dashboard\send_email.php"

    # Get current timestamp in Asia/Singapore timezone
    gmt8_time = datetime.datetime.now(pytz.timezone('Asia/Singapore'))
    timestamp = gmt8_time.strftime('%Y-%m-%d %H:%M:%S')

    # Add timestamp to the data before sending
    pyTOphp["timestamp"] = timestamp  

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
    msg_main = str(message.payload.decode("utf-8"))
    print(f"Received message on topic {message.topic}: {msg_main}")
    
    if message.topic.startswith("POND/EmailNotification"):
        print("Send Email")
        send_email(json.loads(msg_main))

client = mqtt.Client()
client.on_connect = on_connect
client.on_disconnect = on_disconnect
client.on_message = on_message

username = "****"
password = "****"
client.username_pw_set(username, password)

try:
    client.connect("13.214.212.87", 1883, keepalive=60)
except Exception as e:
    print(f"Failed to connect to MQTT broker: {e}")
    exit(1)

client.loop_forever()