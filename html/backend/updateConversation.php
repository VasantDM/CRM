<?php
if (isset($_POST['id']) && isset($_POST['date']) && isset($_POST['adminName']) && isset($_POST['user_conversation']) && isset($_POST['admin_conversation']) && isset($_POST['remarks1']) && isset($_POST['remarks2'])) {
    $id = $_POST['id'];
    $date = $_POST['date'];
    // $time = $_POST['time'];
    $adminName = $_POST['adminName'];
    $user_conversation = $_POST['user_conversation'];
    $admin_conversation = $_POST['admin_conversation'];
    $remarks1 = $_POST['remarks1'];
    $remarks2 = $_POST['remarks2'];

    include 'connection.php';

    // Correct SQL query
    $sql = "UPDATE conversations 
            SET date = ?, adminName = ?, user_conversation = ?, admin_conversation = ?, remarks1 = ?, remarks2 = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    // Check if the statement was prepared successfully
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("ssssssi", $date,  $adminName, $user_conversation, $admin_conversation, $remarks1, $remarks2, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'Invalid input.';
}
?>
