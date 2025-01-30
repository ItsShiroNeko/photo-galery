<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    // header('Location: login.php');
    // exit();
}
$user_id = $_SESSION['user_id'];
$id = $_GET['photo_id'];
$data = mysqli_query($conn, "SELECT * FROM photos WHERE id = '$id'");
$d = mysqli_fetch_assoc($data);
$likes_check = "SELECT * FROM likes WHERE photo_id = $id AND user_id = $user_id";
$likes_check_result = mysqli_query($conn, $likes_check);
$likes_check_finish = mysqli_fetch_assoc($likes_check_result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = $_POST['caption'];
    $description = $_POST['description'];
    // $filename = time() . '_' . $_FILES['filename']['name'];
    // move_uploaded_file($_FILES['filename']['tmp_name'], 'uploads/' . $filename);
    $query = "UPDATE photos SET caption = '$caption', description = '$description' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        header("Location: view_album.php?id=" . $d['album_id']);
        exit;
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <title>Update Foto</title>
</head>

<body>
    <div class="container mt-4">
        <div>
            <h1>Update Foto</h1>
            <form method="POST" enctype="multipart/form-data">
                <label for="caption" class="form-label">Nama</label>
                <input type="text" class="form-control" id="caption" name="caption" value="<?= $d['caption'] ?>"
                    required>
                <br>
                <label for="description" class="form-label">Deskripsi Foto:</label>
                <textarea name="description" id="description" class="form-control"
                    required><?php echo htmlspecialchars($d['description']); ?></textarea>
                <br>
                <!-- <label for="filename" class="form-label">Lokasi File (URL gambar):</label>
                <input type="file" name="filename" id="filename" class="form-control"
                    value="<?php echo htmlspecialchars($d['filename']); ?>"> -->
                <br>
                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="view_album.php?id=<?php echo $d['album_id'] ?>" class="btn btn-warning">Batal</a>
            </form>
            <br>
            <div class="col-md-3">
                <div class="card mb-4 p-2" data-bs-toggle="modal" data-bs-target="#<?php echo $id ?>">
                    <label for="preview" class="form-label"> Foto Sebelumnya:</label>
                    <img id="preview" name="preview" src="uploads/<?php echo $d['filename'] ?>" alt=""
                        style="max-height: 300px; max-width:300px;">
                </div>
                <div class="modal fade" id="<?php echo $id ?>" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                    <p class="card-text">Foto Sebelumnya</p>
                                </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img src="uploads/<?php echo $d['filename']; ?>" class="card-img-top" alt="Photo">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</html>