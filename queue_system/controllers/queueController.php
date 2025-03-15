<?php
require '../config/database.php';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        
        // 1) Add a new queue entry (UPDATED)
        if ($_POST['action'] === 'add') {
            header('Content-Type: application/json');
            $customer_name = trim($_POST['customer_name'] ?? '');
            $service_type = trim($_POST['service_type'] ?? 'general');
            $window_number = trim($_POST['window_number'] ?? 'Window 1'); // New field
            
            if (empty($customer_name)) {
                echo json_encode(['success' => false, 'message' => 'Customer name is required.']);
                exit();
            }
            
            // Validate window number
            $allowed_windows = ['Window 1', 'Window 2', 'Window 3'];
            if (!in_array($window_number, $allowed_windows)) {
                echo json_encode(['success' => false, 'message' => 'Invalid window selection.']);
                exit();
            }

            // Get next ticket number
            $stmt = $pdo->query("SELECT MAX(ticket_number) AS last_ticket FROM queue");
            $row = $stmt->fetch();
            $ticket_number = ($row['last_ticket'] ?? 0) + 1;
            
            // Insert new queue entry
            $stmt = $pdo->prepare("INSERT INTO queue 
                (ticket_number, customer_name, service_type, window_number, status) 
                VALUES (?, ?, ?, ?, 'waiting')");
            
            if ($stmt->execute([$ticket_number, $customer_name, $service_type, $window_number])) {
                echo json_encode(['success' => true, 'message' => "Added to queue with ticket number $ticket_number"]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error.']);
            }
            exit();
        }
        
        // 2) Update status for an existing queue entry
        if ($_POST['action'] === 'update_status') {
            header('Content-Type: application/json');
            $id = $_POST['id'] ?? '';
            $new_status = trim($_POST['new_status'] ?? '');
            $allowed = ['waiting', 'in progress', 'completed'];
            if (empty($id) || !in_array($new_status, $allowed)) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                exit();
            }
            $stmt = $pdo->prepare("UPDATE queue SET status = ? WHERE id = ?");
            if ($stmt->execute([$new_status, $id])) {
                echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
            }
            exit();
        }
        
        // 3) Clear the entire queue and reset the ticket number
        if ($_POST['action'] === 'clear') {
            header('Content-Type: application/json');
            $stmt = $pdo->prepare("TRUNCATE TABLE queue");
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Queue cleared successfully. Ticket number reset.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to clear queue.']);
            }
            exit();
        }
    }
}

// Handle GET requests: Return the queue for viewing (UPDATED)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'view') {
    header('Content-Type: text/html; charset=UTF-8');

    $query = "SELECT * FROM queue";
    $conditions = [];
    $params = [];

    // Filtering
    if (!empty($_GET['filter_status'])) {
        $conditions[] = "status = ?";
        $params[] = $_GET['filter_status'];
    }
    if (!empty($_GET['filter_customer'])) {
        $conditions[] = "customer_name LIKE ?";
        $params[] = "%" . $_GET['filter_customer'] . "%";
    }
    if (!empty($_GET['filter_ticket'])) {
        $conditions[] = "ticket_number = ?";
        $params[] = $_GET['filter_ticket'];
    }
    if (!empty($_GET['filter_service'])) {
        $conditions[] = "service_type = ?";
        $params[] = $_GET['filter_service'];
    }
    if (!empty($_GET['filter_window'])) {
        $conditions[] = "window_number = ?";
        $params[] = $_GET['filter_window'];
    }
    
    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    
    // Sorting
    $allowedSortFields = ['ticket_number', 'customer_name', 'status', 'service_type', 'window_number'];
    $sort_by = 'ticket_number'; // Default sorting column
    if (!empty($_GET['sort_by']) && in_array($_GET['sort_by'], $allowedSortFields)) {
        $sort_by = $_GET['sort_by'];
    }
    $sort_order = 'ASC';
    if (!empty($_GET['sort_order']) && in_array(strtoupper($_GET['sort_order']), ['ASC', 'DESC'])) {
        $sort_order = strtoupper($_GET['sort_order']);
    }
    $query .= " ORDER BY $sort_by $sort_order";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $queue = $stmt->fetchAll();

    $isAdmin = isset($_GET['admin']) && $_GET['admin'] == 1;
    $html = '';
    foreach ($queue as $entry) {
        $html .= "<tr data-id='{$entry['id']}'>";
        $html .= "<td>{$entry['ticket_number']}</td>";
        $html .= "<td>{$entry['customer_name']}</td>";
        $html .= "<td>{$entry['service_type']}</td>";
        $html .= "<td>{$entry['window_number']}</td>";
        $html .= "<td>{$entry['status']}</td>";
        
        if ($isAdmin) {
            $html .= "<td>
                        <select class='form-select status-update' data-id='{$entry['id']}'>";
            foreach (['waiting', 'in progress', 'completed'] as $status) {
                $selected = ($entry['status'] == $status) ? "selected" : "";
                $html .= "<option value='{$status}' {$selected}>{$status}</option>";
            }
            $html .= "</select>
                      </td>";
        }
        $html .= "</tr>";
    }
    echo $html;
    exit();
}

echo "Invalid request.";
exit();