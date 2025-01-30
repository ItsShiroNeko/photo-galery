<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ? debug module jangan lupa di hapus
// echo 'logged as userid: ' . $_SESSION['user_id'];
// if (isset($_SESSION['admin'])) {
//     echo " (admin)";
// } else {
//     echo " (user)";
// }

$album_id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM albums WHERE id = $album_id";
$result = mysqli_query($conn, $query);
$album = mysqli_fetch_assoc($result);

// * membatasi user untuk melihat album user lain
// ! belum diperlukan jadi di comment aja
// if (!$album || $album['user_id'] != $_SESSION['user_id']) {
//     header('Location: index.php');
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $user_id = $_SESSION['user_id'];

    $filename = time() . '_' . $_FILES['photo']['name'];
    move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $filename);

    $query = "INSERT INTO photos (album_id, user_id, filename, caption, description) 
              VALUES ($album_id, $user_id, '$filename', '$caption', '$description')";

    if (mysqli_query($conn, $query)) {
        header("Location: view_album.php?id=$album_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Viewer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f5f7;
            font-family: 'Nunito', sans-serif;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .album-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .form-control,
        .btn {
            border-radius: 8px;
        }

        .card img {
            object-fit: cover;
            height: 200px;
        }

        .card {
            transition: all 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .comment-container {
            width: 300px;
            height: 200px;
            border: 1px solid #ccc;
            padding: 10px;
            overflow-y: auto;
            background-color: #f9f9f9;
        }

        .comment {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        footer {
            height: 50px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Photo Album</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-black" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <h1 class="album-title"><?php echo htmlspecialchars($album['title']); ?></h1>

        <!-- Add Photo Form -->
        <?php if (isset($_SESSION['admin'])): ?>
            <div class="card mb-4 shadow-md">
                <div class="card-header bg-primary text-white text-center">Add Photo (Admin Only)</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Photo</label>
                                <input type="file" name="photo" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Caption</label>
                                <input type="text" name="caption" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="1" required></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Add Photo</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Photos Section -->
        <div class="row">
            <?php
            $query = "SELECT * FROM photos WHERE album_id = $album_id ORDER BY created_at DESC";
            $result = mysqli_query($conn, $query);
            if ($result->num_rows > 0) {



                while ($photo = mysqli_fetch_assoc($result)) {
                    $photo_id = $photo['id'];

                    $comments_query = "SELECT comments.*, users.username FROM comments 
                             JOIN users ON comments.user_id = users.id 
                             WHERE photo_id = $photo_id ORDER BY created_at DESC";
                    $comments_result = mysqli_query($conn, $comments_query);

                    // *
                    $likes_query = "SELECT COUNT(*) as count FROM likes WHERE photo_id = $photo_id";
                    $likes_result = mysqli_query($conn, $likes_query);
                    $likes = mysqli_fetch_assoc($likes_result)['count'];

                    // * cek apakah sudah dilike atau belum
                    $likes_check = "SELECT * FROM likes WHERE photo_id = $photo_id AND user_id = $user_id";
                    $likes_check_result = mysqli_query($conn, $likes_check);
                    $likes_check_finish = mysqli_fetch_assoc($likes_check_result);
                    ?>
                    <div class="col-md-4">
                        <div class="card mb-4 p-2">
                            <img src="uploads/<?php echo $photo['filename']; ?>" class="card-img-top" alt="Photo"
                                data-bs-toggle="modal" data-bs-target="#<?php echo $photo_id ?>>
                            <div class=" card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($photo['caption']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($photo['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <form method="POST" action="like.php" class="d-inline">
                                    <input type="hidden" name="photo_id" value="<?php echo $photo_id; ?>">
                                    <?php if ($likes_check_result->num_rows > 0): ?>
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-thumbs-up"></i> Like (<?php echo $likes; ?>)
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($likes_check_result->num_rows == 0): ?>
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-thumbs-up"></i> Like (<?php echo $likes; ?>)
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>
                            <form method="POST" action="comments.php">
                                <input type="hidden" name="photo_id" value="<?php echo $photo_id; ?>">
                                <div class="input-group mb-3">
                                    <input type="text" name="comment" class="form-control" required>
                                    <button type="submit" class="btn btn-primary">Comment</button>
                                </div>
                            </form>
                            <h6>Comments:</h6>
                            <div class="mt-3 comment-container">
                                <?php
                                if ($comments_result->num_rows > 0) {

                                    while ($comment = mysqli_fetch_assoc($comments_result)): ?>
                                        <div class="mb-2 comment">
                                            <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                                            <?php echo htmlspecialchars($comment['comment']); ?>
                                        </div>
                                    <?php endwhile;
                                } else {
                                    echo "tidak ada komen";
                                }
                                ?>
                            </div>
                            </a><?php if (isset($_SESSION['admin'])): ?>
                                <br>
                                <h6>Admin:</h6>
                                <a href="delete_photo.php?photo_id=<?php echo $photo_id; ?>" class="button delete"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                                <br>
                                <a href="edit_photo.php?photo_id=<?php echo $photo_id; ?>" class="button edit"
                                    onclick="return confirm('Apakah Anda yakin ingin mengedit foto ini?')">
                                    <i class="fas fa-pencil"></i> Edit
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="<?php echo $photo_id ?>" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">
                                        <p class="card-text"><?php echo htmlspecialchars($photo['caption']); ?></p>
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="uploads/<?php echo $photo['filename']; ?>" class="card-img-top" alt="Photo">
                                    <div class="container-fluid">
                                        <?php echo htmlspecialchars($photo['description']); ?>
                                        <br>
                                        <?php if ($likes_check_result->num_rows > 0): ?>
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-thumbs-up"></i> Like (<?php echo $likes; ?>)
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($likes_check_result->num_rows == 0): ?>
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-thumbs-up"></i> Like (<?php echo $likes; ?>)
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="comment-container container-fluid">
                                        <?php
                                        $comments_query = "SELECT comments.*, users.username FROM comments 
                                            JOIN users ON comments.user_id = users.id 
                                            WHERE photo_id = $photo_id ORDER BY created_at DESC";
                                        $comments_result = mysqli_query($conn, $comments_query);
                                        if ($comments_result->num_rows > 0) {
                                            while ($comment = mysqli_fetch_assoc($comments_result)): ?>
                                                <div class="mb-2 comment">
                                                    <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                                                    <?php echo htmlspecialchars($comment['comment']); ?>
                                                    <?php echo "a"; ?>
                                                </div>
                                            <?php endwhile;
                                        } else {
                                            echo "tidak ada komen";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else {
                echo "<h3> tidak ada foto </h3>";
            } ?>
    </div>
    <a href="index.php" class="btn btn-danger mt-3" style="margin-left: 100px;">Back to Albums</a>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
<footer></footer>

</html>