<?php
include('connection.php');

if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    // Fetch conversations for the given user ID
    $sql = "SELECT * FROM conversations WHERE emp_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $conversations = [];
    while ($row = $result->fetch_assoc()) {
        $conversations[] = $row; // Add conversation data to the array
    }

    echo json_encode($conversations); // Return conversations as JSON
} else {
    echo json_encode([]); // Return empty array if no user ID is provided
}
?>
