import pymysql
import paho.mqtt.client as mqtt
import json
import datetime
import pytz
import time

# ----------------------------
# Database Connection
# ----------------------------
def get_db_connection():
    return pymysql.connect(
        host='localhost',
        user='root',
        password='ICPHpass!',
        database='pond_monitoring',
        autocommit=True
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

# ----------------------------
# MQTT Setup
# ----------------------------
def on_connect(client, userdata, flags, rc):
    print(f"Connected with result code {rc}")

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

client = mqtt.Client()
client.on_connect = on_connect
client.on_disconnect = on_disconnect

username = "mqtt"
password = "ICPHmqtt!"
client.username_pw_set(username, password)

try:
    client.connect("13.214.212.87", 1883, keepalive=60)
except Exception as e:
    print(f"Failed to connect to MQTT broker: {e}")
    exit(1)

# ----------------------------
# Feeding Time Checker
# ----------------------------
last_feed_times = {}  # Store last feed times to avoid duplicate triggers

def check_feeding_schedule():
    reconnect_db()
    try:
        query = "SELECT time1, interval1, time2, interval2, time3, interval3 FROM feeder_time WHERE id = 1"
        cursor.execute(query)
        result = cursor.fetchone()

        if result:
            gmt8_time = datetime.datetime.now(pytz.timezone('Asia/Singapore'))
            current_time_str = gmt8_time.strftime("%H:%M")

            schedules = [
                {"time": result[0], "interval": result[1]},
                {"time": result[2], "interval": result[3]},
                {"time": result[4], "interval": result[5]},
            ]

            for i, sched in enumerate(schedules, start=1):
                feed_time = sched["time"]
                interval = sched["interval"]

                if feed_time and interval:
                    if current_time_str == feed_time:
                        if last_feed_times.get(i) != current_time_str:
                            print(f"Feeding time reached: {feed_time} (Interval: {interval} sec)")
                            send_feeding_command(feed_time, interval)
                            last_feed_times[i] = current_time_str
        else:
            print("No feeder time data found for id = 1")

    except Exception as e:
        print("An error occurred while checking feeding schedule:", e)

# ----------------------------
# Send Feeding Command
# ----------------------------
def send_feeding_command(feed_time, interval):
    try:
        feed_data = {
            "feed_time": feed_time,
            "interval": interval
        }
        feed_json = json.dumps(feed_data)
        client.publish("POND/FeederCommand", feed_json)
        print("Feeding command sent:", feed_json)
    except Exception as e:
        print("Failed to send feeding command:", e)

# ----------------------------
# Main Loop
# ----------------------------
if __name__ == "__main__":
    client.loop_start()  # Run MQTT in background

    while True:
        check_feeding_schedule()
        time.sleep(60)  # Check every 1 minute