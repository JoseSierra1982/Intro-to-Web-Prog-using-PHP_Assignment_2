<?php
include 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM contents_info WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SESSION['user_id'] != $post['user_id']) {
    echo "Not enough rights to edit this image.";
    include 'footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $properties = $_POST['properties'];
    $image = $post['image'];

    if ($_FILES['image']['name']) {
        $image = basename($_FILES['image']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . $image;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Image uploaded successfully
        } else {
            echo "Error when uploading image";
        }
    }

    $stmt = $pdo->prepare('UPDATE contents_info SET title = ?, content = ?, properties = ?, image = ? WHERE id = ?');
    $stmt->execute([$title, $content, $properties, $image, $id]);

    header('Location: index.php');
    exit;
}
?>

<h1 class="mb-4">Edit Image</h1>

<form action="update.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
    </div>
    <div class="form-group">
        <label for="content">Content</label>
        <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="properties">Properties</label>
        <textarea class="form-control" id="properties" name="properties" rows="5" required><?php echo htmlspecialchars($post['properties']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="image">Image</label>
        <?php if ($post['image']): ?>
            <div>
                <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="old image" style="max-width: 200px; display: block; margin-bottom: 10px;">
            </div>
        <?php endif; ?>
        <input type="file" class="form-control-file" id="image" name="image">
    </div>
    <button type="submit" class="btn btn-primary">Update Image</button>
</form>

<?php include 'footer.php'; ?>
