<?php
include 'config.php';

if (isset($_GET['photo_id'])) {
    $photo_id = $_GET['photo_id'];
    echo $_GET['photo_id'];

    $query = "SELECT album_id FROM photos WHERE id = $photo_id";
    $result = mysqli_query($conn, $query);
    $photo = mysqli_fetch_assoc($result);
    $album_id = $photo['album_id'];

    $sqlDelete = "DELETE FROM photos WHERE id = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $photo_id);

    if ($stmtDelete->execute()) {
        header("Location: view_album.php?id=$album_id");
        exit();
    } else {
        echo "Gagal menghapus foto: " . $conn->error;
    }
} else {
    echo "ID foto tidak ada.";
    exit();
}
?>