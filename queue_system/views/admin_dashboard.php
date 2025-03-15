<?php
// For testing, login check is commented out.
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Queue Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Queue Management</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link active" href="admin_dashboard.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="dashboard_analytics.php">Analytics</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="reports.php">Reports</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  
  <!-- Theme Toggle Button -->
  <button class="theme-toggle" id="themeToggle">Toggle Theme</button>
  
  <div class="container mt-5">
    <h1 class="mb-4">Admin Dashboard</h1>
    
    <!-- Form to add new queue entry -->
    <form id="addQueueForm" class="mb-4">
      <div class="row">
        <div class="col-md-3">
          <input type="text" id="customer_name" name="customer_name" placeholder="Customer name" class="form-control" required>
        </div>
        <div class="col-md-3">
          <input type="text" id="service_type" name="service_type" placeholder="Service type" class="form-control" required>
        </div>
        <div class="col-md-3">
          <select id="window_number" class="form-select" required>
            <option value="">Select Window</option>
            <option value="Window 1">Window 1</option>
            <option value="Window 2">Window 2</option>
            <option value="Window 3">Window 3</option>
          </select>
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn btn-primary w-100">Add to Queue</button>
        </div>
      </div>
    </form>
    
    <!-- Message Display -->
    <div id="message" class="mb-4"></div>
    
    <!-- Clear Queue Button -->
    <div class="mb-3">
      <button id="clearQueue" class="btn btn-danger w-100">Clear Queue</button>
    </div>
    
    <!-- Filtering Controls -->
    <div class="row mb-3">
      <div class="col-md-3">
        <input type="text" id="filter_ticket" class="form-control" placeholder="Ticket Number">
      </div>
      <div class="col-md-3">
        <input type="text" id="filter_customer" class="form-control" placeholder="Customer Name">
      </div>
      <div class="col-md-3">
        <select id="filter_status" class="form-select">
          <option value="">All Statuses</option>
          <option value="waiting">Waiting</option>
          <option value="in progress">In Progress</option>
          <option value="completed">Completed</option>
        </select>
      </div>
      <div class="col-md-3">
        <input type="text" id="filter_service" class="form-control" placeholder="Service Type">
      </div>
      <div class="col-md-3 mt-2">
        <button id="applyFilters" class="btn btn-primary w-100">Apply Filters</button>
      </div>
    </div>
    
    <!-- Sorting Controls -->
    <div class="row mb-3">
      <div class="col-md-3">
        <select id="sort_by" class="form-select">
          <option value="ticket_number">Sort by Ticket Number</option>
          <option value="customer_name">Sort by Customer Name</option>
          <option value="service_type">Sort by Service Type</option>
          <option value="status">Sort by Status</option>
        </select>
      </div>
      <div class="col-md-3">
        <select id="sort_order" class="form-select">
          <option value="ASC">Ascending</option>
          <option value="DESC">Descending</option>
        </select>
      </div>
    </div>
    
    <!-- Queue Table -->
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>Ticket Number</th>
          <th>Customer Name</th>
          <th>Service Type</th>
          <th>Window</th>
          <th>Status</th>
          <th>Update Status</th>
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
  
  <!-- AJAX for adding to queue -->
  <script>
    $(document).ready(function(){
      $('#addQueueForm').submit(function(e){
        e.preventDefault();
        var customer_name = $('#customer_name').val().trim();
        var service_type = $('#service_type').val().trim();
        var window_number = $('#window_number').val();
        
        if(customer_name === "" || service_type === "" || window_number === ""){
          $('#message').html('<div class="alert alert-danger">All fields are required</div>');
          return;
        }
        $.ajax({
          url: '../controllers/queueController.php',
          type: 'POST',
          data: { 
            action: 'add', 
            customer_name: customer_name, 
            service_type: service_type,
            window_number: window_number
          },
          dataType: 'json',
          success: function(response) {
            if(response.success) {
              $('#message').html('<div class="alert alert-success">'+response.message+'</div>');
              $('#customer_name').val('');
              $('#service_type').val('');
              $('#window_number').val('');
              loadQueue();
            } else {
              $('#message').html('<div class="alert alert-danger">'+response.message+'</div>');
            }
          },
          error: function(){
            $('#message').html('<div class="alert alert-danger">Error adding to queue.</div>');
          }
        });
      });
    });
  </script>
  
  <!-- AJAX to clear the queue (database clear) -->
  <script>
    $(document).on('click', '#clearQueue', function(e) {
      e.preventDefault();
      if (confirm('Are you sure you want to clear the entire queue? This will reset the ticket number.')) {
        $.ajax({
          url: "../controllers/queueController.php",
          type: "POST",
          data: { action: "clear" },
          dataType: "json",
          success: function(response) {
            if(response.success) {
              $('#message').html('<div class="alert alert-success">'+response.message+'</div>');
              loadQueue();
            } else {
              $('#message').html('<div class="alert alert-danger">'+response.message+'</div>');
            }
          },
          error: function() {
            $('#message').html('<div class="alert alert-danger">Error clearing queue.</div>');
          }
        });
      }
    });
  </script>
  
  <!-- AJAX to load queue with filtering & sorting, plus remove completed rows -->
  <script>
    function loadQueue() {
      var filter_ticket = $('#filter_ticket').val().trim();
      var filter_customer = $('#filter_customer').val().trim();
      var filter_status = $('#filter_status').val();
      var filter_service = $('#filter_service').val().trim();
      var sort_by = $('#sort_by').val();
      var sort_order = $('#sort_order').val();
      
      $.ajax({
        url: "../controllers/queueController.php",
        type: "GET",
        data: { 
          action: "view", 
          filter_ticket: filter_ticket,
          filter_customer: filter_customer,
          filter_status: filter_status,
          filter_service: filter_service,
          sort_by: sort_by,
          sort_order: sort_order,
          admin: 1
        },
        success: function(data) {
          $("#queueList").html(data);
          $("#queueList tr").each(function(){
            var status = $(this).find("td:eq(4)").text().trim().toLowerCase();
            if(status === "completed") {
              $(this).remove();
            }
          });
        },
        error: function() {
          $("#queueList").html('<tr><td colspan="6" class="text-danger">Error loading queue.</td></tr>');
        }
      });
    }
    
    $(document).ready(function() {
      loadQueue();
      setInterval(loadQueue, 3000);
      $('#applyFilters').click(function() {
        loadQueue();
      });
    });
  </script>
  
  <!-- AJAX to update queue status -->
  <script>
    $(document).on('change', '.status-update', function(){
      var new_status = $(this).val();
      var id = $(this).data('id');
      
      $.ajax({
        url: "../controllers/queueController.php",
        type: "POST",
        data: { action: "update_status", id: id, new_status: new_status },
        dataType: "json",
        success: function(response) {
          if(response.success) {
            $('#message').html('<div class="alert alert-success">'+response.message+'</div>');
            loadQueue();
          } else {
            $('#message').html('<div class="alert alert-danger">'+response.message+'</div>');
          }
        },
        error: function() {
          $('#message').html('<div class="alert alert-danger">Error updating status.</div>');
        }
      });
    });
  </script>
</body>
</html>