<!-- Modal Script Start -->
<script>
  // Success Error Prompt
  function openSuccessModal() {
    const successModal = document.getElementById('successModal');
    successModal.style.display = 'flex';
  }

  function closeSuccessModal() {
    const successModal = document.getElementById('successModal');
    successModal.style.display = 'none';
  }

  function openErrorModal() {
    const errorModal = document.getElementById('errorModal');
    errorModal.style.display = 'flex';
  }

  function closeErrorModal() {
    const errorModal = document.getElementById('errorModal');
    errorModal.style.display = 'none';
  }

  // Logout
  function logout() {
    alert("Logging out...");
  }

  // Send Interval Modal
  function openSendIntervalModal() {
    $.ajax({
      type: "POST",
      url: "fetch_send_interval.php",
      dataType: "json",
      success: function(response) {
        console.log(response);
        if (response.error) {
          console.error(response.error);
        } else {

          // Set the radio input that matches the server's time_interval
          const radios = document.querySelectorAll('input[name="sendInterval"]');
          radios.forEach(radio => {
            radio.checked = (radio.value === String(response.time_interval));
          });

          const SendIntervalModal = document.getElementById('SendIntervalModal');
          SendIntervalModal.style.display = 'flex';
          const dropdown = document.getElementById('settingsDropdown');
          dropdown.style.display = 'none';

        }
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", error);
      }
    });
  }

  function closeSendIntervalModal() {
    const SendIntervalModal = document.getElementById('SendIntervalModal');
    SendIntervalModal.style.display = 'none';
  }

  function confirmSendIntervalModal() {
    console.log("Saving New Send Interval");
    let formData = new FormData($("#IntervalForm")[0]);

    // Get the selected sendInterval value from the form
    let selectedInterval = formData.get("sendInterval");
    console.log("Selected Interval:", selectedInterval);

    $.ajax({
      type: "POST",
      url: "update_send_interval.php",
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      dataType: "json",
      success: function(response) {
        if (response.status === 'success') {
          console.log(response.message);
          closeSendIntervalModal();
          $("#promptSuccessSM").text("Send Interval Updated Successfully.");
          openSuccessModal();

          // Instead of sending the plain value, send JSON
          var payload = {
            send_interval: selectedInterval
          };

          // Convert object to JSON string
          var message = new Messaging.Message(JSON.stringify(payload));
          message.destinationName = 'POND/SendIntervalResponse';
          message.qos = 0;
          message.retained = true;
          client.send(message);


        } else {
          console.log("Error:", response.message);
          $("#promptErrorSM").text("Failed to Change Send Interval");
          closeSendIntervalModal();
          openErrorModal();
        }
      },
      error: function(xhr, status, error) {
        console.error(xhr.responseText);
        $("#promptErrorSM").text("Failed to Change Send Interval");
        closeSendIntervalModal();
        openErrorModal();
      }
    });
  }


  // Threshold Modal
  function openThresholdModal() {
    $.ajax({
      type: "POST",
      url: "fetch_threshold.php",
      dataType: "json",
      success: function(response) {
        console.log(response);
        if (response.error) {
          console.error(response.error);
        } else {
          $("#waterLow").val(response.wt_low);
          $("#waterHigh").val(response.wt_high);
          $("#phLow").val(response.ph_low);
          $("#phHigh").val(response.ph_high);
          $("#doLow").val(response.do_low);
          $("#doHigh").val(response.do_high);

          const ThresholdModal = document.getElementById('ThresholdModal');
          ThresholdModal.style.display = 'flex';
          const dropdown = document.getElementById('settingsDropdown');
          dropdown.style.display = 'none';
        }
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", error);
      }
    });
  }

  function closeThresholdModal() {
    const ThresholdModal = document.getElementById('ThresholdModal');
    ThresholdModal.style.display = 'none';
  }

  function confirmThresholdModal() {
    console.log("Saving New Threshold");
    let formData = new FormData($("#ThresholdForm")[0]);
    // Extract values from the form
    let payload = {
      wt_low: parseFloat(formData.get("waterLow")),
      wt_high: parseFloat(formData.get("waterHigh")),
      ph_low: parseFloat(formData.get("phLow")),
      ph_high: parseFloat(formData.get("phHigh")),
      do_low: parseFloat(formData.get("doLow")),
      do_high: parseFloat(formData.get("doHigh"))
    };
    $.ajax({
      type: "POST",
      url: "update_threshold.php",
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      dataType: "json",
      success: function(response) {
        if (response.status === 'success') {
          console.log(response.message);
          closeThresholdModal();
          $("#promptSuccessSM").text("Threshold Updated Successfully.");
          openSuccessModal();

          // Send JSON payload to MQTT
          var message = new Messaging.Message(JSON.stringify(payload));
          message.destinationName = 'POND/ThresholdResponse';
          message.qos = 0;
          message.retained = true;
          client.send(message);

        } else {
          console.log("Error:", response.message);
          $("#promptErrorSM").text("Failed to Change Threshold");
          closeThresholdModal();
          openErrorModal();
        }
      },
      error: function(xhr, status, error) {
        console.error(xhr.responseText);
        $("#promptErrorSM").text("Failed to Change Threshold");
        closeThresholdModal();
        openErrorModal();
      }
    });
  }

  // Feeder Time Modal
  function openFeederTimeModal() {
    $.ajax({
      type: "POST",
      url: "fetch_feeder.php",
      dataType: "json",
      success: function(response) {
        console.log(response);
        if (response.error) {
          console.error(response.error);
        } else {

          // Populate the input fields
          if (response.time1) $("#feedTime1").val(response.time1);
          if (response.interval1) $("#feedInterval1").val(response.interval1);

          if (response.time2) $("#feedTime2").val(response.time2);
          if (response.interval2) $("#feedInterval2").val(response.interval2);

          if (response.time3) $("#feedTime3").val(response.time3);
          if (response.interval3) $("#feedInterval3").val(response.interval3);

          const FeederTimeModal = document.getElementById('FeederTimeModal');
          FeederTimeModal.style.display = 'flex';
          const dropdown = document.getElementById('settingsDropdown');
          dropdown.style.display = 'none';
        }
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", error);
      }
    });
  }

  function closeFeederTimeModal() {
    const FeederTimeModal = document.getElementById('FeederTimeModal');
    FeederTimeModal.style.display = 'none';
  }

  function confirmFeederTimeModal() {
    console.log("Saving New Feeder Configuration");
    let formData = new FormData($("#FeederTimeForm")[0]);
    $.ajax({
      type: "POST",
      url: "update_feeder.php",
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      dataType: "json",
      success: function(response) {
        if (response.status === 'success') {
          console.log(response.message);
          closeFeederTimeModal();
          $("#promptSuccessSM").text("Feeder Updated Successfully.");
          openSuccessModal();
        } else {
          console.log("Error:", response.message);
          $("#promptErrorSM").text("Failed to Change Feeder");
          closeFeederTimeModal();
          openErrorModal();
        }
      },
      error: function(xhr, status, error) {
        console.error(xhr.responseText);
        $("#promptErrorSM").text("Failed to Change Feeder");
        closeFeederTimeModal();
        openErrorModal();
      }
    });
  }


  // DATE RANGE
  function openDateRangePickerModal() {
    $("#DateRangeForm")[0].reset();
    const DateRangePickerModal = document.getElementById('DateRangePickerModal');
    DateRangePickerModal.style.display = 'flex';
  }

  function closeDateRangePickerModal() {
    const DateRangePickerModal = document.getElementById('DateRangePickerModal');
    DateRangePickerModal.style.display = 'none';
  }

  function confirmDateRangePickerModal() {
    console.log("Confirm Date Range")
    updateChart();
    closeDateRangePickerModal();
    document.getElementById('start_date').value = "";
    document.getElementById('end_date').value = "";
  }



  // EXPORT DATA MODAL START HERE

  // Choices Modal
  function openExportChoicesModal() {
    const ExportChoicesModal = document.getElementById('ExportChoicesModal');
    ExportChoicesModal.style.display = 'flex';
  }

  function closeExportChoicesModal() {
    const ExportChoicesModal = document.getElementById('ExportChoicesModal');
    ExportChoicesModal.style.display = 'none';
  }

  // Historical Export Modal
  function openExportDataModal() {
    $("#ExportDataForm")[0].reset();
    closeExportChoicesModal();
    const ExportDataModal = document.getElementById('ExportDataModal');
    ExportDataModal.style.display = 'flex';
  }

  function closeExportDataModal() {
    const ExportDataModal = document.getElementById('ExportDataModal');
    ExportDataModal.style.display = 'none';
  }

  async function confirmExportDataModal() {
    try {
      let export_start_date = document.getElementById('export_start_date').value;
      let export_end_date = document.getElementById('export_end_date').value;
      let response = await fetch(`download_excel.php?start_date=${export_start_date}&end_date=${export_end_date}`);
      if (!response.ok) {
        throw new Error('Failed to fetch data');
      }

      const data = await response.json();

      if (data.length === 0) {
        // alert('No data available to export.');
        $("#promptErrorSM").text('No data available to export.');
        openErrorModal();
        return;
      }

      // Generate Excel file
      const rows = [
        ['Water Temperature', 'pH Level', 'Dissolved Oxygen', 'Timestamp'],
        ...data.map(row => [
          row.wt,
          row.ph,
          row.do,
          row.timestamp
        ])
      ];

      const csvContent = rows
        .map(row => row.map(value => `"${value}"`).join(','))
        .join('\n');

      const blob = new Blob([csvContent], {
        type: 'text/csv;charset=utf-8;'
      });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', "sensor_data" + `.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      closeExportDataModal();
    } catch (error) {
      console.error('Error downloading Excel:', error);
    }
  }



  // Threshold Data Export Modal
  function openExportThresholdModal() {
    console.log("TH DATA OPEN MODAL");
    $("#ThresholdDataForm")[0].reset();
    closeExportChoicesModal();
    const ExportThresholdModal = document.getElementById('ExportThresholdModal');
    ExportThresholdModal.style.display = 'flex';
  }

  function closeExportThresholdModal() {
    const ExportThresholdModal = document.getElementById('ExportThresholdModal');
    ExportThresholdModal.style.display = 'none';
  }

  async function confirmExportThresholdModal() {
    try {
      let threshold_start_date = document.getElementById('threshold_start_date').value;
      let threshold_end_date = document.getElementById('threshold_end_date').value;
      let response = await fetch(`download_excel_th.php?start_date=${threshold_start_date}&end_date=${threshold_end_date}`);
      if (!response.ok) {
        throw new Error('Failed to fetch data');
      }

      const data = await response.json();

      if (data.length === 0) {
        // alert('No data available to export.');
        $("#promptErrorSM").text('No data available to export.');
        openErrorModal();
        return;
      }

      // Generate Excel file
      const rows = [
        ['Sensor', 'Value', 'Status', 'Timestamp'],
        ...data.map(row => [
          row.sensor,
          row.value,
          row.status,
          row.timestamp
        ])
      ];

      const csvContent = rows
        .map(row => row.map(value => `"${value}"`).join(','))
        .join('\n');

      const blob = new Blob([csvContent], {
        type: 'text/csv;charset=utf-8;'
      });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', "sensor_th_data" + `.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      closeExportThresholdModal();
    } catch (error) {
      console.error('Error downloading Excel:', error);
    }
  }
</script>

<!-- Modal Script End -->