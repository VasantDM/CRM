<?php
session_start();
// Destroy the session and clear session variables
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
// Destroy the session
if (session_destroy()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to destroy session']);
}
?>
