<?php
include 'connection.php';

// Query to fetch notifications (join with customer table for validation)
$query = "SELECT e.cust_id, e.callDate, e.callTime, e.details, c.id 
          FROM emp_contact e 
          INNER JOIN customer c ON e.cust_id = c.id
          WHERE e.callDate >= CURDATE() 
          ORDER BY e.callDate DESC, e.callTime DESC";

$result = $conn->query($query);

$notifications = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// Return notifications as a JSON response
header('Content-Type: application/json');
echo json_encode($notifications);

$conn->close();
?>
