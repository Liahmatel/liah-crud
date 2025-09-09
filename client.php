<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "book_inventory_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Browse Books</title>
    <style>
        body { font-family: Arial; background:#f4f4f4; padding:20px; }
        .book { background:#fff; padding:15px; margin:10px; border-radius:6px; box-shadow:0 0 5px #ccc; }
        h2 { margin:0; }
        a { text-decoration:none; color:blue; }
    </style>
</head>
<body>
    <h1>Browse Books</h1>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="book">
            <h2><?= htmlspecialchars($row['title']) ?></h2>
            <p><b>Author:</b> <?= htmlspecialchars($row['author']) ?></p>
            <p><b>Price:</b> KES <?= number_format($row['price'], 2) ?></p>
            <?php if ($row['file_path']): ?>
                <a href="<?= $row['file_path'] ?>" target="_blank">⬇ Download</a>
            <?php else: ?>
                <span>No file available</span>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</body>
</html>
