<?php
include 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'];

if (!is_numeric($id)) {
    echo "Invalid ID.";
    include 'footer.php';
    exit;
}


$stmt = $pdo->prepare('SELECT * FROM contents_info WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "This image does not exist.";
    include 'footer.php';
    exit;
}

if ($_SESSION['user_id'] != $post['user_id']) {
    echo "Not enough right to edit this image.";
    include 'footer.php';
    exit;
}


if ($post['image']) {
    $image_path = 'uploads/' . $post['image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}


$stmt = $pdo->prepare('DELETE FROM contents_info WHERE id = ?');
$stmt->execute([$id]);

// Main Page redirection
header('Location: index.php');
include 'footer.php';
?>
