<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Некорректный ID рецепта");
}

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->execute([$id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipe) {
        die("❌ Рецепт не найден");
    }
} catch (Exception $e) {
    die("❌ Ошибка загрузки рецепта: " . htmlspecialchars($e->getMessage()));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cooking_time = (int)($_POST['cooking_time'] ?? 0);
    $ingredients = trim($_POST['ingredients'] ?? '');
    $status = $_POST['status'] ?? 'не приготовлен';
    $difficulty = $_POST['difficulty'] ?? 'легко';

    $allowed_status = ['не приготовлен', 'приготовлен'];
    $allowed_difficulty = ['легко', 'средне', 'сложно'];

    if (empty($title)) {
        $error = "❌ Название обязательно!";
    } elseif (empty($ingredients)) {
        $error = "❌ Ингредиенты обязательны!";
    } elseif (!in_array($status, $allowed_status)) {
        $error = "❌ Недопустимое значение статуса.";
    } elseif (!in_array($difficulty, $allowed_difficulty)) {
        $error = "❌ Недопустимое значение сложности.";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE recipes 
                SET title = ?, description = ?, cooking_time = ?, ingredients = ?, status = ?, difficulty = ? 
                WHERE id = ?
            ");
            $stmt->execute([$title, $description, $cooking_time, $ingredients, $status, $difficulty, $id]);
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $error = "❌ Ошибка обновления: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>✏️ Редактировать рецепт</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>✏️ Редактировать: <?= htmlspecialchars($recipe['title']) ?></h2>
        <a href="index.php" class="btn btn-secondary">⬅️ Назад</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Название рецепта <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($recipe['title']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Описание</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($recipe['description']) ?></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Время приготовления (мин)</label>
                <input type="number" name="cooking_time" class="form-control" min="1" value="<?= (int)$recipe['cooking_time'] ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Сложность</label>
                <select name="difficulty" class="form-select">
                    <option value="легко" <?= ($recipe['difficulty'] == 'легко') ? 'selected' : '' ?>>Легко</option>
                    <option value="средне" <?= ($recipe['difficulty'] == 'средне') ? 'selected' : '' ?>>Средне</option>
                    <option value="сложно" <?= ($recipe['difficulty'] == 'сложно') ? 'selected' : '' ?>>Сложно</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="не приготовлен" <?= ($recipe['status'] == 'не приготовлен') ? 'selected' : '' ?>>Не приготовлен</option>
                    <option value="приготовлен" <?= ($recipe['status'] == 'приготовлен') ? 'selected' : '' ?>>Приготовлен</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Ингредиенты <span class="text-danger">*</span></label>
            <textarea name="ingredients" class="form-control" rows="6" required><?= htmlspecialchars($recipe['ingredients']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">💾 Сохранить изменения</button>
        <a href="index.php" class="btn btn-secondary">Отмена</a>
    </form>
</div>
</body>
</html>