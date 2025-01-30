<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $photo_id = mysqli_real_escape_string($conn, $_POST['photo_id']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $user_id = $_SESSION['user_id'];

    $query = "SELECT album_id FROM photos WHERE id = $photo_id";
    $result = mysqli_query($conn, $query);
    $photo = mysqli_fetch_assoc($result);
    $album_id = $photo['album_id'];

    $query = "INSERT INTO comments (photo_id, user_id, comment) VALUES ($photo_id, $user_id, '$comment')";
    mysqli_query($conn, $query);

    header("Location: view_album.php?id=$album_id");
    exit();
}

header('Location: index.php');
exit();
?>