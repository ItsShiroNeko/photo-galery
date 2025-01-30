<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Create Album - Photo Album</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">Create New Album</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Album Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Album</button>
                    <a href="index.php" class="btn btn-warning">Back</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO albums (user_id, title) VALUES ($user_id, '$title')";

    if (mysqli_query($conn, $query)) {
        header('Location: index.php');
        exit();
    }
}
?>