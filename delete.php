<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Некорректный ID");
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>