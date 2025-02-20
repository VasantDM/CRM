<?php
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    include 'connection.php';
    // Delete query
    $sql = "DELETE FROM conversations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
    $conn->close();
}
?>
