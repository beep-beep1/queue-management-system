<?php
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get start and end dates from GET parameters
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    
    // Basic validation for date input
    if (!$start_date || !$end_date) {
        die("Please provide start_date and end_date in YYYY-MM-DD format.");
    }
    
    // Query completed queues within the date range
    $stmt = $pdo->prepare("SELECT * FROM queue WHERE status = 'completed' AND DATE(created_at) BETWEEN ? AND ? ORDER BY created_at ASC");
    $stmt->execute([$start_date, $end_date]);
    $results = $stmt->fetchAll();
    
    // Set CSV headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=report_' . $start_date . '_to_' . $end_date . '.csv');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Column headers for CSV
    fputcsv($output, ['Ticket Number', 'Customer Name', 'Status', 'Created At']);
    
    // Write data rows to CSV
    foreach ($results as $row) {
        fputcsv($output, [$row['ticket_number'], $row['customer_name'], $row['status'], $row['created_at']]);
    }
    
    fclose($output);
    exit();
}

echo "Invalid request.";
exit();
?>
