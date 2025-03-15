<?php
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'data') {
    header('Content-Type: application/json');
    
    // Total number of completed queues
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM queue WHERE status = 'completed'");
    $totalCompleted = $stmt->fetch()['total'];
    
    // Customer flow by hour for today
    $stmt = $pdo->query("SELECT HOUR(created_at) as hour, COUNT(*) as count FROM queue WHERE DATE(created_at) = CURDATE() GROUP BY HOUR(created_at) ORDER BY hour ASC");
    $hourData = $stmt->fetchAll();
    
    // Customer flow by day for the past 7 days
    $stmt = $pdo->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM queue WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY date ASC");
    $dayData = $stmt->fetchAll();
    
    // Prepare data arrays for Chart.js
    $hours = [];
    $hourCounts = [];
    foreach ($hourData as $row) {
        $hours[] = $row['hour'];
        $hourCounts[] = $row['count'];
    }
    
    $dates = [];
    $dayCounts = [];
    foreach ($dayData as $row) {
        $dates[] = $row['date'];
        $dayCounts[] = $row['count'];
    }
    
    $data = [
        'totalCompleted' => $totalCompleted,
        'hourChart' => [
            'labels' => $hours,
            'data'   => $hourCounts,
        ],
        'dayChart' => [
            'labels' => $dates,
            'data'   => $dayCounts,
        ],
    ];
    
    echo json_encode($data);
    exit();
}

echo json_encode(['error' => 'Invalid request']);
exit();
?>
