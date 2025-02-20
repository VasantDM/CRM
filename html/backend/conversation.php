<?php
header('Content-Type: application/json');

// Include the database connection
include('connection.php');
session_start();

// Check if the user is logged in and the `number` is set in the session
if (!isset($_SESSION['number'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}
// print_r(session_start());exit;   

$number = $_SESSION['number']; // Get the logged-in user's phone number
$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
// print_r($number);exit;
// Fetch the emp_id of the logged-in user using their phone number
if (!$is_admin) {
$result = $conn->query("SELECT id,name,number FROM employees WHERE number = '$number' LIMIT 1");
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'User not found in the employees table.']);
    exit;
}

$row = $result->fetch_assoc();
$emp_id = $row['id']; // Get the emp_id of the logged-in user
$adminName = $row['name']; // Employee Name
$number = $row['number']; // Employee number
}
else {
    // If user is an admin, assign a placeholder name
    $adminName = "Admin";
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['cust_id'])) {
    // Fetch conversations for the given cust_id and the logged-in emp_id
    $cust_id = $conn->real_escape_string($_GET['cust_id']); // Sanitize the input
    $date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : ''; // Get the date if provided

    // Start the SQL query based on whether a date is provided
    if ($date) {
        // If a date is provided, fetch conversations for the given cust_id and date
        $sql = "SELECT id, adminName, date, TIME_FORMAT(time, '%H:%i') as time, user_conversation, admin_conversation,remarks1,remarks2,status,cust_id, emp_id ,number
                FROM conversations 
                WHERE cust_id = '$cust_id' AND date = '$date'";
    } else {
        // If no date is provided, fetch all conversations for the given cust_id
        $sql = "SELECT id, adminName, date, TIME_FORMAT(time, '%H:%i') as time, user_conversation, admin_conversation,remarks1,remarks2,status,cust_id, emp_id ,number
                FROM conversations 
                WHERE cust_id = '$cust_id'";
    }

    // Execute the query
    $result = $conn->query($sql);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['is_editable'] = ($is_admin || $row['emp_id'] == $emp_id); 
        $data[] = $row;
    }

    echo json_encode($data);


} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store new conversation data
    $cust_id = $conn->real_escape_string($_POST['cust_id']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $user_convo = $conn->real_escape_string($_POST['user_conversation']);
    $admin_convo = $conn->real_escape_string($_POST['admin_conversation']);
    $remarks1 = $conn->real_escape_string($_POST['remarks1']);
    $remarks2 = $conn->real_escape_string($_POST['remarks2']);

    // Insert data into the database with the emp_id
    $query = "INSERT INTO conversations (cust_id, emp_id, adminName, date, time, user_conversation, admin_conversation,remarks1,remarks2, number) 
              VALUES ('$cust_id', '$emp_id', '$adminName', '$date', '$time', '$user_convo', '$admin_convo','$remarks1','$remarks2', '$number')";

    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
