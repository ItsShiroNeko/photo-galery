<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $photo_id = mysqli_real_escape_string($conn, $_POST['photo_id']);
    $user_id = $_SESSION['user_id'];

    $query = "SELECT album_id FROM photos WHERE id = $photo_id";
    $result = mysqli_query($conn, $query);
    $photo = mysqli_fetch_assoc($result);
    $album_id = $photo['album_id'];

    $check_query = "SELECT * FROM likes WHERE photo_id = $photo_id AND user_id = $user_id";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) == 0) {
        $query = "INSERT INTO likes (photo_id, user_id) VALUES ($photo_id, $user_id)";
        mysqli_query($conn, $query);
    } else {
        $query = "DELETE FROM likes WHERE photo_id = $photo_id AND user_id = $user_id";
        mysqli_query($conn, $query);
    }
    header("Location: view_album.php?id=$album_id");
    exit();
}
?>