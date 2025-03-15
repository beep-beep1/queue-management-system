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
  <title>Dashboard Analytics - Queue Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <!-- Theme Toggle Button -->
  <button class="theme-toggle" id="themeToggle">Toggle Theme</button>
  
  <!-- Navigation Bar (optional) -->
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
            <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="dashboard_analytics.php">Analytics</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="reports.php">Reports</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  
  <div class="container mt-5">
    <h1>Dashboard Analytics</h1>
    
    <div class="row my-4">
      <div class="col-md-4">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">Total Completed</h5>
            <p class="card-text fs-1" id="totalCompleted">Loading...</p>
          </div>
        </div>
      </div>
    </div>
    
    <h3>Customer Flow by Hour (Today)</h3>
    <canvas id="hourChart" width="400" height="200"></canvas>
    
    <h3 class="mt-5">Customer Flow by Day (Last 7 Days)</h3>
    <canvas id="dayChart" width="400" height="200"></canvas>
    
  </div>
  
  <!-- jQuery and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Theme Toggle Script -->
  <script>
    const themeToggle = document.getElementById("themeToggle");
    themeToggle.addEventListener("click", function() {
      document.body.classList.toggle("dark");
      if (document.body.classList.contains("dark")) {
        localStorage.setItem("theme", "dark");
      } else {
        localStorage.setItem("theme", "light");
      }
    });
    if (localStorage.getItem("theme") === "dark") {
      document.body.classList.add("dark");
    }
  </script>
  
  <!-- Load analytics data and render charts -->
  <script>
    function loadAnalytics() {
      $.ajax({
        url: "../controllers/analyticsController.php",
        type: "GET",
        data: { action: "data" },
        dataType: "json",
        success: function(data) {
          // Display total completed
          $('#totalCompleted').text(data.totalCompleted);
          
          // Convert 0–23 hours to 12-hour AM/PM format
          var hourLabels = data.hourChart.labels.map(function(h) {
            var hourInt = parseInt(h);
            if (hourInt === 0) {
              return "12 AM";
            } else if (hourInt < 12) {
              return hourInt + " AM";
            } else if (hourInt === 12) {
              return "12 PM";
            } else {
              return (hourInt - 12) + " PM";
            }
          });
          
          // Hour Chart (Bar)
          var ctxHour = document.getElementById('hourChart').getContext('2d');
          new Chart(ctxHour, {
            type: 'bar',
            data: {
              labels: hourLabels,
              datasets: [{
                label: 'Customers',
                data: data.hourChart.data,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
              }]
            },
            options: {
              scales: { 
                y: { 
                  beginAtZero: true, 
                  ticks: { precision: 0 } 
                } 
              }
            }
          });
          
          // Day Chart (Line)
          var ctxDay = document.getElementById('dayChart').getContext('2d');
          new Chart(ctxDay, {
            type: 'line',
            data: {
              labels: data.dayChart.labels,
              datasets: [{
                label: 'Customers',
                data: data.dayChart.data,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                fill: true
              }]
            },
            options: {
              scales: { 
                y: { 
                  beginAtZero: true,
                  ticks: { precision: 0 }
                }
              }
            }
          });
        },
        error: function() {
          alert("Failed to load analytics data.");
        }
      });
    }
    
    $(document).ready(function(){
      loadAnalytics();
    });
  </script>
</body>
</html>
