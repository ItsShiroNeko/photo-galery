<?php
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    // header('Location: login.php');
    // exit();
}

// * start debug module
// ? debug module jangan lupa di hapus
// echo 'logged as userid: ';
// if (isset($_SESSION['admin'])) {
//     echo " (admin)";
// } else {
//     echo " (user)";
// }
// * end debug module
?>
<!DOCTYPE html>
<html>

<head>
    <title>Photo Album</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Photo Album</a>
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['admin'])): ?>
                    <a class="nav-link" href="create_album.php">Create Album</a>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text text-sm">Silahkan Login atau Resgister!</h2>
        <br>
        <?php
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $query = "SELECT * FROM albums";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                if (isset($_SESSION['admin'])) {
                    echo '<div class="row">';
                    while ($album = mysqli_fetch_assoc($result)) {
                        $get = $album['title'];
                        $getuser = mysqli_query($conn, "SELECT u.username FROM users u JOIN albums a ON u.id = a.user_id WHERE a.title = '$get';");
                        $auser = mysqli_fetch_assoc($getuser);
                        $username = $auser['username'];
                        echo '<div class="col-md-4 mb-4">';
                        echo '<div class="card">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($album['title']) . '</h5>';
                        echo '<h6> Created By: ' . $username . '</h6>';
                        echo '<a href="view_album.php?id=' . $album['id'] . '" class="btn btn-primary">View Album</a>'; ?>
                        <a href="delete_album.php?id=<?php echo $album['id'] ?>" class="btn btn-sm btn-danger"
                            style="margin-left:100px;" onclick="return confirm('Apakah Anda yakin ingin menghapus album ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                        <!-- <a href="edit_album.php?id=<?php echo $album['id'] ?>" class="btn btn-sm btn-warning"
                            onclick="return confirm('Apakah Anda yakin ingin mengedit album ini?')"><i class="bi bi-pencil"></i></a> -->
                        <?php echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    // $result = mysqli_query($conn, "SELECT * FROM albums WHERE user_id = '$user_id'");
                    echo '<div class="row">';
                    while ($album = mysqli_fetch_assoc($result)) {
                        echo '<div class="col-md-4 mb-4">';
                        echo '<div class="card">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($album['title']) . '</h5>';
                        echo '<a href="view_album.php?id=' . $album['id'] . '" class="btn btn-primary">View Album</a>'; ?>
                        <?php echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            } else {
                echo '<p>No albums yet. <a href="create_album.php">Create one</a>!</p>';
            }
        } else {
        }
        ?>
    </div>
</body>

</html>