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
        password='',
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

def on_connect(client, userdata, flags, rc):
    print(f"Connected with result code {rc}")
    client.subscribe("POND/SensorData")
    client.subscribe("POND/ReqThreshold")
    client.subscribe("POND/ReqSendInterval")
    client.subscribe("POND/AllRelayStatus")

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
    # print(f"Message received on topic: {message.topic}")
    msg_main = str(message.payload.decode("utf-8"))
    print(f"Received message on topic {message.topic}: {msg_main}")
    
    if message.topic.startswith("POND/SensorData"):
        print("Sensor data received")
        process_data(message.topic, msg_main)
    elif message.topic.startswith("POND/ReqThreshold"):
        print("Threshold has been requested")
        get_threshold()
    elif message.topic.startswith("POND/ReqSendInterval"):
        print("Send Interval has been requested")
        get_send_interval()
    elif message.topic.startswith("POND/AllRelayStatus"):
        print("AllRelayStatus received")
        process_all_relay_status(msg_main)
        
def process_all_relay_status(msg_main):
    # Parse JSON first (keep JSON-related errors here)
    try:
        json_msg_main = json.loads(msg_main)
    except json.JSONDecodeError as e:
        print(f"Failed to decode JSON: {e}")
        return

    # Get feeder value (accept "1" or 1)
    feeder_val = json_msg_main.get("feeder", 0)
    feeder_is_one = str(feeder_val).strip() == "1"

    if not feeder_is_one:
        return  # nothing to do if feeder != 1

    # DB ops + publish
    reconnect_db()
    try:
        # Decrement but never below zero
        cursor.execute("""
            UPDATE feeder_count
            SET count = CASE WHEN count > 0 THEN count - 1 ELSE 0 END
            WHERE id = 1
        """)
        conn.commit()

        # Read back the new count
        cursor.execute("SELECT count FROM feeder_count WHERE id = 1")
        row = cursor.fetchone()
        new_count = int(row[0]) if row else 0

        # Publish retained update
        payload = json.dumps({"feeder_count": new_count})
        client.publish("POND/FeederCount", payload, qos=0, retain=True)
        print(f"[Feeder] Decremented. New count={new_count}. Published to FeederCount.")

    except Exception as e:
        print(f"An unexpected error occurred during feeder update: {e}")
        conn.rollback()

def process_data(topic, msg_main):
    try:
        json_msg_main = json.loads(msg_main)
        water_temperature = float(json_msg_main["wt"])
        ph_level = float(json_msg_main["ph"])
        dissolved_oxygen = float(json_msg_main["do"])

        print(f"water_temperature: {water_temperature}, ph_level: {ph_level}, dissolved_oxygen: {dissolved_oxygen}")
        insert_data(water_temperature, ph_level, dissolved_oxygen)

    except json.JSONDecodeError as e:
        print(f"Failed to decode JSON: {e}")
    except KeyError as e:
        print(f"Missing key in JSON data: {e}")
    except TypeError as e:
        print(f"Unexpected data type: {e}")
    except Exception as e:
        print(f"An unexpected error occurred: {e}")

def insert_data(water_temperature, ph_level, dissolved_oxygen):
    reconnect_db()
    gmt8_time = datetime.datetime.now(pytz.timezone('Asia/Singapore'))
    timestamp = gmt8_time.strftime('%Y-%m-%d %H:%M:%S')

    try:
        insert_query = f"INSERT INTO `sensor_data` (wt, ph, do, timestamp) VALUES (%s, %s, %s, %s)"
        cursor.execute(insert_query, (water_temperature, ph_level, dissolved_oxygen, timestamp))
        conn.commit()
        print(f"Insert Success")
        client.publish("POND/WebDataUpdate", "NewDataInserted")
    except Exception as e:
        print("An error occurred:", e)
        conn.rollback()
        
def get_threshold():
    reconnect_db()
    # conn.commit()
    try:
        query = "SELECT wt_low, wt_high, ph_low, ph_high, do_low, do_high FROM threshold WHERE id = 1"
        cursor.execute(query)
        result = cursor.fetchone()
        if result:
            threshold_data = {
                "wt_low": result[0],
                "wt_high": result[1],
                "ph_low": result[2],
                "ph_high": result[3],
                "do_low": result[4],
                "do_high": result[5]
            }
            threshold_json = json.dumps(threshold_data)
            client.publish("POND/ThresholdResponse", threshold_json)
            print("Threshold sent:", threshold_json)
        else:
            print("No threshold data found for id = 1")
    except Exception as e:
        print("An error occurred while fetching threshold:", e)
        
def get_send_interval():
    reconnect_db()
    # conn.commit()
    try:
        query = "SELECT time_interval FROM sending_interval WHERE id = 1"
        cursor.execute(query)
        result = cursor.fetchone()
        if result:
            send_interval_data = {
                "send_interval": result[0]
            }
            send_interval_data_json = json.dumps(send_interval_data)
            client.publish("POND/SendIntervalResponse", send_interval_data_json)
            print("Send Interval sent:", send_interval_data_json)
        else:
            print("No send interval data found for id = 1")
    except Exception as e:
        print("An error occurred while fetching send interval:", e)

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