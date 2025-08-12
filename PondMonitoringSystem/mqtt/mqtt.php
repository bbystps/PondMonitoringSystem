<script>
    var i = 0;
    var client = new Messaging.Client("13.214.212.87", 9001, "myclientid_" + parseInt(Math.random() * 100, 10));

    client.onConnectionLost = function(responseObject) {
        toastr.error('Trying to reconnect...', 'Server not responding.', {
            closeButton: true,
            timeOut: 3000,
            progressBar: true,
            allowHtml: true
        });
        MQTTreconnect();
    };

    function MQTTreconnect() {
        if (client.connected) {
            return;
        }
        //console.log("ATTEMPTING TO RECONNECT");
        // Set a timeout before attempting to reconnect
        setTimeout(function() {
            // Try to reconnect
            client.connect(options);
        }, 5000); // You can adjust the timeout duration as needed
    }

    //Connect Options
    var options = {
        timeout: 3,
        keepAliveInterval: 60,
        userName: '****',
        password: '****',
        onSuccess: function() {
            client.subscribe('POND/SystemControl', {
                qos: 0
            });
            client.subscribe('POND/WebDataUpdate', {
                qos: 0
            });
            client.subscribe('POND/AllRelayStatus', {
                qos: 0
            });
            client.subscribe('POND/SystemControl', {
                qos: 0
            });
            toastr.success('', 'Server OK!');

            var message = new Messaging.Message('New Message');
            message.destinationName = 'POND/serverInitiate';
            message.qos = 0;
            client.send(message);
        },

        onFailure: function(message) {
            toastr.error('Trying to reconnect...', 'Server not responding.', {
                closeButton: true,
                timeOut: 3000,
                progressBar: true,
                allowHtml: true
            });
            MQTTreconnect();
        }

    };

    var publish = function(payload, topic, qos) {
        var message = new Messaging.Message(payload);
        message.destinationName = topic;
        message.qos = qos;
        client.send(message);
    }

    // // Flag to prevent re-sending when updating from MQTT
    // let isUpdatingFromMQTT = false;

    // function handleModeToggle(checkbox) {
    //     if (isUpdatingFromMQTT) return; // Skip publish if we're just syncing from MQTT

    //     const mode = checkbox.checked ? "Auto" : "Manual";
    //     console.log("Mode changed to:", mode);

    //     // Send via MQTT
    //     var message = new Messaging.Message(mode);
    //     message.destinationName = 'POND/SystemControl';
    //     message.qos = 0;
    //     message.retained = true;
    //     client.send(message);
    // }

    client.onMessageArrived = function(message) {
        var x = message.payloadString;
        console.log(x);

        // Sync Toggle Switch to retain value
        if (message.destinationName == "POND/SystemControl") {
            // Sync the toggle state based on retained message
            // isUpdatingFromMQTT = true;
            document.getElementById("modeToggle").checked = (x === "Auto");
            // isUpdatingFromMQTT = false;

            const isAuto = document.getElementById("modeToggle").checked;
            if (isAuto) {
                console.log("Currently in Auto mode");

                document.getElementById("btn_pump").disabled = true;
                document.getElementById("btn_aerator").disabled = true;
                document.getElementById("btn_feeder").disabled = true;
            } else {
                console.log("Currently in Manual mode");

                document.getElementById("btn_pump").disabled = false;
                document.getElementById("btn_aerator").disabled = false;
                document.getElementById("btn_feeder").disabled = false;
            }
        }
        // Web Update for new sensor data
        else if (message.destinationName == "POND/WebDataUpdate") {
            DisplaySensorData();
            // fetchLatestData();
            updateChart();
        }

        // Relay Triggering 
        else if (message.destinationName == "POND/AllRelayStatus") {
            var ParsedData = JSON.parse(x);
            console.log(ParsedData.pump);
            console.log(ParsedData.aerator);
            console.log(ParsedData.feeder);

            if (ParsedData.pump == "1") {
                changeButtonColor("btn_pump", "#ef4444");
                changeStatusColor("pump_status", "#10b981");
                document.getElementById("btn_pump").disabled = false;
                document.getElementById("btn_pump").innerText = "Stop Pump";
            } else if (ParsedData.pump == "0") {
                changeButtonColor("btn_pump", "#10b981");
                changeStatusColor("pump_status", "#282828");
                document.getElementById("btn_pump").disabled = false;
                document.getElementById("btn_pump").innerText = "Start Pump";
            }

            if (ParsedData.aerator == "1") {
                changeButtonColor("btn_aerator", "#ef4444");
                changeStatusColor("aerator_status", "#10b981");
                document.getElementById("btn_aerator").disabled = false;
                document.getElementById("btn_aerator").innerText = "Stop Aerator";
            } else if (ParsedData.aerator == "0") {
                changeButtonColor("btn_aerator", "#10b981");
                changeStatusColor("aerator_status", "#282828");
                document.getElementById("btn_aerator").disabled = false;
                document.getElementById("btn_aerator").innerText = "Start Aerator";
            }


            if (ParsedData.feeder == "1") {
                changeButtonColor("btn_feeder", "#ef4444");
                changeStatusColor("feeder_status", "#10b981");
                document.getElementById("btn_feeder").disabled = false;
                document.getElementById("btn_feeder").innerText = "Stop Feeder";
            } else if (ParsedData.feeder == "0") {
                changeButtonColor("btn_feeder", "#10b981");
                changeStatusColor("feeder_status", "#282828");
                document.getElementById("btn_feeder").disabled = false;
                document.getElementById("btn_feeder").innerText = "Start Feeder";
            }
        }

    }
</script>