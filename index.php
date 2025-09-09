<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "book_inventory_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle Add Book
if (isset($_POST['add'])) {
    $title  = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $price  = $conn->real_escape_string($_POST['price']);

    $file_path = NULL;
    if (!empty($_FILES['file']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir);
        $file_path = $uploadDir . time() . "_" . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
    }

    $conn->query("INSERT INTO books (title, author, price, file_path) VALUES ('$title', '$author', '$price', '$file_path')");
    header("Location: index.php");
    exit();
}

// Handle Update Book
if (isset($_POST['update'])) {
    $id     = (int)$_POST['id'];
    $title  = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $price  = $conn->real_escape_string($_POST['price']);

    $file_sql = "";
    if (!empty($_FILES['file']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir);
        $file_path = $uploadDir . time() . "_" . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path);
        $file_sql = ", file_path='$file_path'";
    }

    $conn->query("UPDATE books SET title='$title', author='$author', price='$price' $file_sql WHERE id=$id");
    header("Location: index.php");
    exit();
}

// Handle Delete Book
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM books WHERE id=$id");
    header("Location: index.php");
    exit();
}

// Fetch all books
$result = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Book Inventory</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f4f4; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; background: #fff; margin-top:20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #333; color: #fff; }
        button { padding: 8px 15px; margin:5px; background: #333; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #555; }
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); }
        .modal-content { background:#fff; margin:10% auto; padding:20px; width:400px; border-radius:8px; }
        .close { float:right; cursor:pointer; font-size:20px; }
        input[type="text"], input[type="number"], input[type="file"] { width:100%; padding:8px; margin:5px 0; }
    </style>
</head>
<body>

<h1>Admin - Book Inventory</h1>

<!-- Add Book Button -->
<button onclick="openModal('addModal')"> Add Book</button>

<!-- Books Table -->
<table>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Author</th>
        <th>Price</th>
        <th>File</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['author']) ?></td>
            <td>KES <?= number_format($row['price'], 2) ?></td>
            <td>
                <?php if ($row['file_path']): ?>
                    <a href="<?= $row['file_path'] ?>" target="_blank">Download</a>
                <?php else: ?> N/A <?php endif; ?>
            </td>
            <td>
                <button onclick="openEditModal(<?= $row['id'] ?>,'<?= htmlspecialchars($row['title']) ?>','<?= htmlspecialchars($row['author']) ?>','<?= $row['price'] ?>')"> Edit</button>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this book?')" style="color:red;"> Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h2>Add Book</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Book Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <input type="file" name="file" accept=".pdf,.epub">
            <button type="submit" name="add">Save</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h2>Edit Book</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="editId">
            <input type="text" name="title" id="editTitle" required>
            <input type="text" name="author" id="editAuthor" required>
            <input type="number" step="0.01" name="price" id="editPrice" required>
            <input type="file" name="file" accept=".pdf,.epub">
            <button type="submit" name="update">Update</button>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).style.display = "block";
}
function closeModal(id) {
    document.getElementById(id).style.display = "none";
}
function openEditModal(id, title, author, price) {
    document.getElementById('editId').value = id;
    document.getElementById('editTitle').value = title;
    document.getElementById('editAuthor').value = author;
    document.getElementById('editPrice').value = price;
    openModal('editModal');
}
</script>

</body>
</html>
