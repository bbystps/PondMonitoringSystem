<!-- SendingInterval Modal -->
<div id="SendIntervalModal" class="modal">
  <div class="modal-content modal-xs">
    <div class="modal-header">Sending Interval in Minutes</div>
    <div class="modal-body">
      <form id="IntervalForm" class="interval-grid" enctype="multipart/form-data">
        <label class="interval-option">5 Minutes
          <input type="radio" name="sendInterval" value="5" checked>
          <span class="checkmark"></span>
        </label>
        <label class="interval-option">10 Minutes
          <input type="radio" name="sendInterval" value="10">
          <span class="checkmark"></span>
        </label>
        <label class="interval-option">30 Minutes
          <input type="radio" name="sendInterval" value="30">
          <span class="checkmark"></span>
        </label>
        <label class="interval-option">60 Minutes
          <input type="radio" name="sendInterval" value="60">
          <span class="checkmark"></span>
        </label>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn-color-blue" onclick="closeSendIntervalModal();">Cancel</button>
      <button class="btn-color-blue" onclick="confirmSendIntervalModal()">Confirm</button>
    </div>
  </div>
</div>

<!-- Threshold Configuration Modal -->
<div id="ThresholdModal" class="modal">
  <div class="modal-content modal-sm">
    <div class="modal-header">Threshold Configuration</div>
    <div class="modal-body">
      <form id="ThresholdForm" class="threshold-grid" enctype="multipart/form-data">

        <!-- Water Level -->
        <div class="threshold-item">
          <div class="threshold-label">Water Level</div>
          <input type="number" id="waterLow" name="waterLow" placeholder="Low" class="threshold-input">
          <input type="number" id="waterHigh" name="waterHigh" placeholder="High" class="threshold-input">
        </div>

        <!-- pH Level -->
        <div class="threshold-item">
          <div class="threshold-label">pH Level</div>
          <input type="number" id="phLow" name="phLow" placeholder="Low" class="threshold-input">
          <input type="number" id="phHigh" name="phHigh" placeholder="High" class="threshold-input">
        </div>

        <!-- Dissolved Oxygen -->
        <div class="threshold-item">
          <div class="threshold-label">Dissolved Oxygen</div>
          <input type="number" id="doLow" name="doLow" placeholder="Low" class="threshold-input">
          <input type="number" id="doHigh" name="doHigh" placeholder="High" class="threshold-input">
        </div>

      </form>
    </div>
    <div class="modal-footer">
      <button class="btn-color-blue" onclick="closeThresholdModal();">Cancel</button>
      <button class="btn-color-blue" onclick="confirmThresholdModal()">Confirm</button>
    </div>
  </div>
</div>


<!-- Feeder Time Settings Modal -->
<div id="FeederTimeModal" class="modal">
  <div class="modal-content modal-sm">
    <div class="modal-header">Feeder Time Settings</div>
    <div class="modal-body">
      <form id="FeederTimeForm" class="feeder-grid" enctype="multipart/form-data">
        <div class="feeder-row">
          <label class="feeder-label" for="feedTime1">Time & Interval(seconds) - 1</label>
          <div class="feeder-input-row">
            <input id="feedTime1" name="feedTime1" type="time" class="feeder-input" step="60" required>
            <input id="feedInterval1" name="feedInterval1" class="feeder-input" placeholder="min" required>
          </div>
        </div>
        <div class="feeder-row">
          <label class="feeder-label" for="feedTime2">Time & Interval(seconds) - 2</label>
          <div class="feeder-input-row">
            <input id="feedTime2" name="feedTime2" type="time" class="feeder-input" step="60" required>
            <input id="feedInterval2" name="feedInterval2" class="feeder-input" placeholder="min" required>
          </div>
        </div>
        <div class="feeder-row">
          <label class="feeder-label" for="feedTime3">Time & Interval(seconds) - 3</label>
          <div class="feeder-input-row">
            <input id="feedTime3" name="feedTime3" type="time" class="feeder-input" step="60" required>
            <input id="feedInterval3" name="feedInterval3" class="feeder-input" placeholder="min" required>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn-color-blue" onclick="closeFeederTimeModal();">Cancel</button>
      <button class="btn-color-blue" onclick="confirmFeederTimeModal()">Confirm</button>
    </div>
  </div>
</div>


<!-- Date Range Picker Modal -->
<div id="DateRangePickerModal" class="modal">
  <div class="modal-content modal-xs">
    <div class="modal-header">Select Date Range</div>
    <div class="modal-body">
      <form id="DateRangeForm">
        <div class="DateRange">
          <label for="start_date">Start Date:</label>
          <input id="start_date" name="start_date" type="date">
        </div>
        <div class="DateRange mt-10">
          <label for="end_date">End Date:</label>
          <input id="end_date" name="end_date" type="date">
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn-color-blue" onclick="closeDateRangePickerModal();">Cancel</button>
      <button class="btn-color-blue" onclick="confirmDateRangePickerModal()">Confirm</button>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal">
  <div class="modal-content modal-sm">
    <div class="modal-center">
      <i class="ep--success-filled"></i>
      <h3 id="promptSuccess">Success!</h3>
      <h4 id="promptSuccessSM"></h4>
      <button class="btn-color-green" onclick="closeSuccessModal();">OK</button>
    </div>
  </div>
</div>
<!-- Error Modal -->
<div id="errorModal" class="modal">
  <div class="modal-content modal-sm">
    <div class="modal-center">
      <i class="material-symbols--error"></i>
      <h3 id="promptError">Failed!</h3>
      <h4 id="promptErrorSM"></h4>
      <button class="btn-color-red" onclick="closeErrorModal()">OK</button>
    </div>
  </div>
</div>


<div id="ExportChoicesModal" class="modal">
  <div class="modal-content modal-xs">
    <div class="modal-header">Export Data</div>
    <div class="modal-body">
      <div class="modal-choices">
        <div class="choices-list" onclick="openExportDataModal();">
          Historical Data
        </div>
        <div class="choices-list" onclick="openExportThresholdModal();">
          Threshold Data
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-color-blue" onclick="closeExportChoicesModal();">Cancel</button>
    </div>
  </div>
</div>

<!-- Export Data Modal -->
<div id="ExportDataModal" class="modal">
  <div class="modal-content modal-xs">
    <div class="modal-header">Export Data</div>
    <div class="modal-body">
      <form id="ExportDataForm">
        <div class="DateRange">
          <label for="export_start_date">Start Date:</label>
          <input id="export_start_date" name="export_start_date" type="date">
        </div>
        <div class="DateRange mt-10">
          <label for="export_end_date">End Date:</label>
          <input id="export_end_date" name="export_end_date" type="date">
        </div>
        <!-- <div class="ExportFormat mt-10">
          <label for="export_format">File Format:</label>
          <select id="export_format" name="export_format">
            <option value="csv">CSV</option>
            <option value="xlsx">Excel (.xlsx)</option>
            <option value="json">JSON</option>
          </select>
        </div> -->
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn-color-blue" onclick="closeExportDataModal();">Cancel</button>
      <button class="btn-color-blue" onclick="confirmExportDataModal()">Export</button>
    </div>
  </div>
</div>

<!-- Export Threshold Modal -->
<div id="ExportThresholdModal" class="modal">
  <div class="modal-content modal-xs">
    <div class="modal-header">Export Threshold Data</div>
    <div class="modal-body">
      <form id="ThresholdDataForm">
        <div class="DateRange">
          <label for="threshold_start_date">Start Date:</label>
          <input id="threshold_start_date" name="threshold_start_date" type="date">
        </div>
        <div class="DateRange mt-10">
          <label for="threshold_end_date">End Date:</label>
          <input id="threshold_end_date" name="threshold_end_date" type="date">
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn-color-blue" onclick="closeExportThresholdModal();">Cancel</button>
      <button class="btn-color-blue" onclick="confirmExportThresholdModal()">Export</button>
    </div>
  </div>
</div>