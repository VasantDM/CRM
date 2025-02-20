<?php
// Database connection
require 'connection.php'; // Ensure this file correctly establishes a $conn variable
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all employees
    $result = $conn->query("SELECT id, name, number, gender FROM employees order by id desc");

    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    echo json_encode($users); // Return all users as a JSON response
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add a new employee
    $name = $_POST['name'];
    $number = $_POST['number'];
    $password = $_POST['password']; // Directly storing password (not hashed as per your request)
    $gender = $_POST['gender'];

    // Validate inputs
    if (empty($name) || empty($number) || empty($password) || empty($gender)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO employees (name, number, password, gender) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $number, $password, $gender);

    if ($stmt->execute()) {
        // If insertion is successful, return the added employee's data
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $stmt->insert_id, // Get the last inserted ID
                'name' => $name,
                'number' => $number,
                'gender' => $gender
            ]
        ]);
    } else {
        // If insertion fails, return an error message
        echo json_encode(['success' => false, 'message' => 'Failed to add employee.']);
    }

    $stmt->close();
}
// Check if emp_id is passed in the URL
if (isset($_GET['emp_id'])) {
    $emp_id = $_GET['emp_id'];

    // Fetch the employee's conversations using emp_id
    $sql = "SELECT c.id, c.message, c.timestamp, e.name AS employee_name, e.number AS employee_number
            FROM conversations c
            JOIN employees e ON c.emp_id = e.id
            WHERE c.emp_id = ?"; // Use the emp_id to fetch relevant conversations

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $emp_id); // Bind the emp_id to the query
    $stmt->execute();
    $result = $stmt->get_result();

    $conversations = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $conversations[] = $row;
        }
        echo json_encode($conversations); // Return the conversation data as JSON
    } else {
        echo json_encode(['success' => false, 'message' => 'No conversations found for this employee.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'emp_id parameter is missing.']);
}

$conn->close();

?>
