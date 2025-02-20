<?php
header('Content-Type: application/json');

// Include the database connection
include('connection.php');
// $currentDate = date('Y-m-d'); // Format: YYYY-MM-DD
// Check if it's a POST request to store data or a GET request to fetch data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store new call data
    $cust_id = isset($_POST['cust_id']) ? $conn->real_escape_string($_POST['cust_id']) : '';
    $callDate = isset($_POST['callDate']) ? $conn->real_escape_string($_POST['callDate']) : '';
    $callTime = isset($_POST['callTime']) ? $conn->real_escape_string($_POST['callTime']) : '';
    $details = isset($_POST['details']) ? $conn->real_escape_string($_POST['details']) : '';

    // Check if required fields are set
    if (empty($cust_id) || empty($callDate) || empty($callTime) || empty($details)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Insert data into the database
    $query = "INSERT INTO emp_contact (cust_id, callDate, callTime, details) 
              VALUES ('$cust_id', '$callDate', '$callTime', '$details')";

    if ($conn->query($query)) {
        echo json_encode(['success' => true, 'message' => 'Call details added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save call details.', 'error' => $conn->error]);
    }
}

// Check if it's a GET request to fetch data
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get email parameter from the URL
    $cust_id = isset($_GET['cust_id']) ? $conn->real_escape_string($_GET['cust_id']) : '';

    // Check if the email is provided in the URL
    if (empty($cust_id)) {
        echo json_encode(['error' => 'cust_id is required to fetch notifications.']);
        exit;
    }

    // Fetch notifications (call details) for the email
    $query = "SELECT id,  DATE_FORMAT(callDate, '%d-%m-%Y') as callDate,TIME_FORMAT(callTime, '%H:%i') as callTime,cust_id, details FROM emp_contact WHERE cust_id = '$cust_id' and callDate >= CURDATE() ORDER BY callDate DESC";
    $result = $conn->query($query);

    // Check if the query was successful
    if (!$result) {
        echo json_encode(['error' => 'Database query failed: ' . $conn->error]);
        exit;
    }

    // Check if any data is returned
    if ($result->num_rows > 0) {
        $calls = [];
        while ($row = $result->fetch_assoc()) {
            $calls[] = $row;
        }
        echo json_encode($calls);
    } else {
        echo json_encode(['error' => 'No call details found for this email.']);
    }
}

$conn->close();
?>
