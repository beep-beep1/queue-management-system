<?php
// (Login check omitted for testing. Re-enable if needed.)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Export Reports - Queue Management</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <!-- Theme Toggle Button -->
  <button class="theme-toggle" id="themeToggle">Toggle Theme</button>
  
  <div class="container mt-5">
    <h1>Export Reports</h1>
    <form id="reportForm" method="GET" action="../controllers/reportController.php">
      <div class="row mb-3">
        <div class="col-md-4">
          <label for="start_date" class="form-label">Start Date</label>
          <input type="date" id="start_date" name="start_date" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label for="end_date" class="form-label">End Date</label>
          <input type="date" id="end_date" name="end_date" class="form-control" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Export CSV</button>
    </form>
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
</body>
</html>
