<?php
include 'config.php';

if (isset($_GET['id'])) {
    $albumId = $_GET['id'];
    $sql = "DELETE FROM albums WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $albumId);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ID album tidak ditemukan.";
}




?>