<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer View - Queue Management</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <!-- Theme Toggle Button -->

  <!-- HEADER START -->
  <div class="container mt-4">
    <div class="row align-items-center">
      <!-- Left Seal (optional) -->
      <div class="col-md-2 text-center">
        <!-- If you have a municipal seal image, uncomment and update the src -->
        <img src="../assets/img/municipality_seal.png" alt="Municipality Seal" style="max-width:80px;">
      </div>
      <!-- Center Text -->
      <div class="col-md-8 text-center">
        <h5 class="mt-2 mb-0">COLEGIO DE LAS NAVAS</h5> 
        <h6 class="mt-2 mb-0">(Community College)</h6>
        <p class="mt-2 mb-0">Office of the College Registrar</p>
      </div>
      <!-- Right Seal (optional) -->
      <div class="col-md-2 text-center">
        <!-- If you have a college seal image, uncomment and update the src -->
        <img src="../assets/img/colegio_seal.png" alt="Colegio Seal" style="max-width:80px;">
      </div>
    </div>
    <hr class="mt-3">
  </div>
  <!-- HEADER END -->
  
  <div class="container mt-3">
    <h2 class="mb-4">Customer Queue</h2>
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
  
  <!-- AJAX to load queue and remove completed rows immediately -->
  <script>
    function loadQueue() {
      $.ajax({
        url: "../controllers/queueController.php",
        type: "GET",
        data: { action: "view" },
        success: function(data) {
          $("#queueList").html(data);
          // Immediately remove rows with status "completed"
          $("#queueList tr").each(function(){
            var status = $(this).find("td:eq(4)").text().trim().toLowerCase();
            if(status === "completed") {
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
      setInterval(loadQueue, 3000); // Refresh every 3 seconds
    });
  </script>
</body>
</html>