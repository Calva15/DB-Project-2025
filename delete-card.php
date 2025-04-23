<?php
require_once 'includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if the card exists
$stmt = $conn->prepare("SELECT id FROM cards WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Card not found.");
}

// Delete the card and cascade market prices (via FK)
$stmt = $conn->prepare("DELETE FROM cards WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: view-cards.php?deleted=1");
    exit;
} else {
    die("Delete failed: " . $conn->error);
}
