<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Некорректный ID");
}

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT status FROM recipes WHERE id = ?");
    $stmt->execute([$id]);
    $current_status = $stmt->fetchColumn();

    if (!$current_status) {
        die("Рецепт не найден");
    }
    
    $new_status = ($current_status === 'приготовлен') ? 'не приготовлен' : 'приготовлен';

    $stmt = $pdo->prepare("UPDATE recipes SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);

} catch (Exception $e) {
    die("Ошибка обновления статуса: " . htmlspecialchars($e->getMessage()));
}

// Перенаправляем обратно на главную
header("Location: index.php");
exit;
?>