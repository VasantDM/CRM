<?php
session_start();
// Database connection
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add User functionality
    $name = $_POST['name'];
    $password = $_POST['password'];
    $number = $_POST['number'];
    $categories = $_POST['categories'];

    // Validate inputs
    if (empty($name) || empty($categories) || empty($password) || empty($number)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO customer (name,categories, password, number) VALUES (?,?, ?, ?)");
    $stmt->bind_param("sssd", $name,$categories, $password, $number);  // Fix bind_param types for string data

    if ($stmt->execute()) {
        // Return success response with the added user data
        echo json_encode([
            'success' => true,
            'data' => [
                'name' => $name,
                'categories' => $categories,
                'password' => $password,
                'number' => $number
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add user.']);
    }
    
    $stmt->close();
}else {
    // Fetch Users functionality (GET request)
    $query = "
        SELECT 
            customer.*, 
            COUNT(conversations.cust_id) AS conversationCount 
        FROM customer
        LEFT JOIN conversations ON customer.id = conversations.cust_id
        GROUP BY customer.id
        ORDER BY customer.id DESC";
    
    $result = $conn->query($query);

    $users = [];

    if ($result->num_rows > 0) {
        // Fetch all users and their conversation counts
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    // Return the users data as JSON
    echo json_encode($users);
}

$conn->close();
?>