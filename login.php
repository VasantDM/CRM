<?php
include '../email-chimp/html/backend/connection.php';
session_start();

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $number = $_POST['number'];
    $password = $_POST['password'];

    // Check for admin login (if applicable)
    if ($number === '1234567890' && $password === 'Admin@1122') {
        // session_start();
        session_regenerate_id(true);  // Refresh session to prevent conflicts
    
        $_SESSION['admin'] = true;  // Ensure admin flag is set
        $_SESSION['number'] = $number;  // Ensure the number is stored
        $_SESSION['role'] = 'admin';  // ✅ Optional role-based check
    
        echo "1";  // Admin login successful
        exit;
    }else {
        // Check for credentials in the `customers` table
        $sqlCustomer = "SELECT * FROM customer WHERE number = ? AND password = ?";
        $stmtCustomer = $conn->prepare($sqlCustomer);
        $stmtCustomer->bind_param("ds", $number, $password);
        $stmtCustomer->execute();
        $resultCustomer = $stmtCustomer->get_result();

        // Check for credentials in the `employees` table
        $sqlEmployee = "SELECT * FROM employees WHERE number = ? AND password = ?";
        $stmtEmployee = $conn->prepare($sqlEmployee);
        $stmtEmployee->bind_param("ds", $number, $password);
        $stmtEmployee->execute();
        $resultEmployee = $stmtEmployee->get_result();

        if ($resultCustomer->num_rows > 0) {
            // Login successful for customer
            $customer = $resultCustomer->fetch_assoc();
            $_SESSION['id'] = $customer['id'];
            $_SESSION['number'] = $customer['number'];
          
            echo "2";  // Customer login successful
        } elseif ($resultEmployee->num_rows > 0) {
            // Login successful for employee
            $employee = $resultEmployee->fetch_assoc();
            $_SESSION['user_id'] = $employee['id'];
            $_SESSION['number'] = $employee['number'];
          
            echo "3";  // Employee login successful
        } else {
            // Wrong credentials
            echo "0";  // Invalid credentials
        }

        $stmtCustomer->close();
        $stmtEmployee->close();
    }
}

$conn->close();
?>
