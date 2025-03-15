<?php
// Customer Dashboard: No login required in this example.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard - Join Queue</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <!-- Theme Toggle Button -->
  <button class="theme-toggle btn btn-secondary" id="themeToggle">Toggle Theme</button>
  
  <div class="container mt-5">
    <h1 class="text-center mb-4">Join the Queue</h1>
    
    <!-- Form for Customer to Join Queue -->
    <form id="joinQueueForm" class="mb-4">
      <div class="row g-3">
        <div class="col-12 col-md-4">
          <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Enter your name" required>
        </div>
        <div class="col-12 col-md-4">
          <input type="text" id="service_type" name="service_type" class="form-control" placeholder="Enter service needed (e.g., TOR, GWA, Evaluation of Grades)" required>
        </div>
        <div class="col-12 col-md-4">
          <select id="window_selection" name="window_selection" class="form-control" required>
            <option value="">Select Window</option>
            <option value="Window 1">Window 1</option>
            <option value="Window 2">Window 2</option>
            <option value="Window 3">Window 3</option>
          </select>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-12 text-center">
          <button type="submit" class="btn btn-primary w-100">Add to Queue</button>
        </div>
      </div>
    </form>
    
    <!-- Message Display -->
    <div id="message" class="mb-4"></div>
    
    <!-- Display Queue (Read-Only) -->
    <h2 class="text-center mb-3">Current Queue</h2>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Ticket Number</th>
          <th>Customer Name</th>
          <th>Service Type</th>
          <th>Window</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="queueList">
        <!-- Queue data loaded via AJAX -->
      </tbody>
    </table>
  </div>
  
  <!-- jQuery and Bootstrap JS Bundle -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Theme Toggle Script -->
  <script>
    const themeToggle = document.getElementById("themeToggle");
    themeToggle.addEventListener("click", function() {
      document.body.classList.toggle("dark");
      localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
    });
    if (localStorage.getItem("theme") === "dark") {
      document.body.classList.add("dark");
    }
  </script>
  
  <!-- AJAX to join the queue -->
  <script>
    $(document).ready(function(){
      $('#joinQueueForm').submit(function(e){
        e.preventDefault();
        var customer_name = $('#customer_name').val().trim();
        var service_type = $('#service_type').val().trim();
        var window_selection = $('#window_selection').val();
        
        if(customer_name === "" || service_type === "" || window_selection === ""){
          $('#message').html('<div class="alert alert-danger">Please fill in all fields.</div>');
          return;
        }
        
        $.ajax({
          url: '../controllers/queueController.php',
          type: 'POST',
          data: { action: 'add', customer_name: customer_name, service_type: service_type, window_selection: window_selection },
          dataType: 'json',
          success: function(response) {
            if(response.success) {
              $('#message').html('<div class="alert alert-success">'+response.message+'</div>');
              // Reset the form fields after successful submission
              $('#joinQueueForm')[0].reset(); // This will reset all fields including the window selection
              loadQueue(); // refresh the displayed queue
            } else {
              $('#message').html('<div class="alert alert-danger">'+response.message+'</div>');
            }
          },
          error: function(){
            $('#message').html('<div class="alert alert-danger">Error joining the queue.</div>');
          }
        });
      });
    });
  </script>
  
  <!-- AJAX to load queue (read-only) -->
  <script>
    function loadQueue() {
      $.ajax({
        url: "../controllers/queueController.php",
        type: "GET",
        data: { action: "view" },
        success: function(data) {
          $("#queueList").html(data);
          $("#queueList tr").each(function(){
            var status = $(this).find("td:eq(4)").text().trim().toLowerCase();
            if(status === "completed"){
              $(this).remove();
            }
          });
        },
        error: function() {
          $("#queueList").html('<tr><td colspan="5" class="text-danger">Error loading queue.</td></tr>');
        }
      });
    }
    
    $(document).ready(function() {
      loadQueue();
      setInterval(loadQueue, 3000);
    });
  </script>
</body>
</html>