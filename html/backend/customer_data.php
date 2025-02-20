<?php
session_start();

include('connection.php');

// Check if the session customer number exists
if (!isset($_SESSION['number'])) {
    echo json_encode(['error' => 'Session customer number not found']);
    exit;
}

$customerNumber = $_SESSION['number'];

// Step 1: Fetch the customer ID from the `customer` table
$sqlCustomer = "SELECT id FROM customer WHERE number = ?";
$stmtCustomer = $conn->prepare($sqlCustomer);
$stmtCustomer->bind_param('d', $customerNumber);
$stmtCustomer->execute();
$resultCustomer = $stmtCustomer->get_result();

if ($resultCustomer->num_rows === 0) {
    echo json_encode(['error' => 'Customer not found']);
    exit;
}

$customerRow = $resultCustomer->fetch_assoc();
$customerId = $customerRow['id'];

// Step 2: Fetch conversations where `cust_id` matches the customer's ID
$sqlConversations = "SELECT * FROM conversations WHERE cust_id = ?";
$stmtConversations = $conn->prepare($sqlConversations);
$stmtConversations->bind_param('i', $customerId);
$stmtConversations->execute();
$resultConversations = $stmtConversations->get_result();

$conversations = [];
while ($row = $resultConversations->fetch_assoc()) {
    $conversations[] = $row;
}

// Step 3: Return the conversations as JSON
echo json_encode($conversations);

?>
