#!/usr/bin/env python3
import json
import time
import datetime
import pytz
import pymysql
import paho.mqtt.client as mqtt

# ===================== Config =====================
DB_CFG = dict(
    host="localhost",
    user="root",
    password="",
    database="pond_monitoring",
    autocommit=True,
)

BROKER_HOST = "13.214.212.87"
BROKER_PORT = 1883
BROKER_USER = "mqtt"
BROKER_PASS = "ICPHmqtt!"

# Topics
TOPIC_REQ_SEND_INTERVAL   = "POND/ReqSendInterval"
TOPIC_SEND_INTERVAL_RESP  = "POND/SendIntervalResponse"  # payload: {"send_interval": <minutes>}
TOPIC_DEVICE_STATUS       = "POND/DeviceStatus"          # retained JSON output

# Polling cadence for DB check
DB_CHECK_PERIOD_SEC = 60

# Timezone (UTC+8)
TZ = pytz.timezone("Asia/Manila")
# ==================================================


# ---------- DB helpers ----------
def get_db_connection():
    return pymysql.connect(**DB_CFG)

def reconnect_db(conn, cursor):
    try:
        conn.ping(reconnect=True)
        return conn, conn.cursor()
    except Exception:
        conn = get_db_connection()
        return conn, conn.cursor()

def read_time_interval_minutes(cursor) -> int:
    """
    Reads sending_interval.time_interval (varchar) for id=1 as MINUTES (int).
    """
    cursor.execute("SELECT time_interval FROM sending_interval WHERE id = 1")
    row = cursor.fetchone()
    if not row or row[0] is None:
        return 1  # safe default = 1 minute
    try:
        return max(1, int(str(row[0]).strip()))
    except Exception:
        return 1

def read_latest_sensor_timestamp(cursor):
    """
    Returns latest timestamp from sensor_data (as tz-aware datetime in Asia/Manila),
    or None if table is empty.
    'timestamp' column is a VARCHAR('YYYY-MM-DD HH:MM:SS').
    """
    # Lexicographic MAX() works with ISO-like format used here
    cursor.execute("SELECT MAX(`timestamp`) FROM sensor_data")
    row = cursor.fetchone()
    if not row or row[0] is None:
        return None

    ts_str = str(row[0]).strip()
    # Parse string -> naive datetime
    try:
        naive_dt = datetime.datetime.strptime(ts_str, "%Y-%m-%d %H:%M:%S")
    except ValueError:
        # If formatting is off, bail
        return None

    return TZ.localize(naive_dt)


# ---------- MQTT + Monitor ----------
class DeviceStatusMonitor:
    def __init__(self):
        # DB
        self.conn = get_db_connection()
        self.cursor = self.conn.cursor()

        # Cached interval (minutes)
        self.time_interval_min = read_time_interval_minutes(self.cursor)
        print(f"[INIT] time_interval (minutes) = {self.time_interval_min}")

        # Status cache to avoid spam publishes
        self.last_status = None  # "online" | "offline" | None

        # MQTT
        self.client = mqtt.Client()
        self.client.username_pw_set(BROKER_USER, BROKER_PASS)
        self.client.on_connect = self.on_connect
        self.client.on_disconnect = self.on_disconnect
        self.client.on_message = self.on_message

        # Connect & start loop
        self.client.connect(BROKER_HOST, BROKER_PORT, keepalive=60)
        self.client.loop_start()

        # Optional: ask other service for current interval on boot (one-time sync)
        # If nobody replies, we keep using the DB value.
        time.sleep(0.3)
        self.client.publish(TOPIC_REQ_SEND_INTERVAL, "req", qos=0, retain=False)

    # ----- MQTT callbacks -----
    def on_connect(self, client, userdata, flags, rc):
        print(f"[MQTT] Connected rc={rc}")
        client.subscribe(TOPIC_SEND_INTERVAL_RESP)

    def on_disconnect(self, client, userdata, rc):
        print(f"[MQTT] Disconnected rc={rc}. Reconnect handled by client.loop().")

    def on_message(self, client, userdata, msg):
        if msg.topic == TOPIC_SEND_INTERVAL_RESP:
            # Expect {"send_interval": <minutes>}
            try:
                payload = json.loads(msg.payload.decode("utf-8"))
                if "send_interval" in payload:
                    new_min = int(str(payload["send_interval"]).strip())
                    if new_min > 0 and new_min != self.time_interval_min:
                        self.time_interval_min = new_min
                        print(f"[MQTT] Updated time_interval (minutes) = {self.time_interval_min}")
            except Exception as e:
                print(f"[MQTT] Failed to parse SendIntervalResponse: {e}")

    # ----- Core logic -----
    def compute_status(self):
        # Make sure DB is alive
        self.conn, self.cursor = reconnect_db(self.conn, self.cursor)

        latest_ts = read_latest_sensor_timestamp(self.cursor)
        now = datetime.datetime.now(TZ)

        if latest_ts is None:
            return "offline", None, None

        elapsed_sec = (now - latest_ts).total_seconds()
        elapsed_min = elapsed_sec / 60.0

        status = "offline" if elapsed_min > self.time_interval_min else "online"
        return status, latest_ts.strftime("%Y-%m-%d %H:%M:%S"), round(elapsed_min, 2)

    def publish_status(self, status, latest_ts_str, elapsed_min):
        if status != self.last_status:
            payload = {
                "status": status,                 # "online" | "offline"
                "time_interval_min": self.time_interval_min,
                "last_data_ts": latest_ts_str,    # may be None
                "elapsed_min": elapsed_min        # may be None
            }
            self.client.publish(
                TOPIC_DEVICE_STATUS,
                json.dumps(payload),
                qos=0,
                retain=True
            )
            print(f"[STATUS] {payload}")
            self.last_status = status

    def run(self):
        try:
            while True:
                status, last_ts_str, elapsed_min = self.compute_status()
                self.publish_status(status, last_ts_str, elapsed_min)
                time.sleep(DB_CHECK_PERIOD_SEC)
        except KeyboardInterrupt:
            print("Exiting...")
        finally:
            self.client.loop_stop()
            try:
                self.client.disconnect()
            except Exception:
                pass
            try:
                self.cursor.close()
                self.conn.close()
            except Exception:
                pass


if __name__ == "__main__":
    monitor = DeviceStatusMonitor()
    monitor.run()
