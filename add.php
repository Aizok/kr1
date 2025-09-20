<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cooking_time = (int)($_POST['cooking_time'] ?? 0);
    $ingredients = trim($_POST['ingredients'] ?? '');
    $status = $_POST['status'] ?? 'не приготовлен';
    $difficulty = $_POST['difficulty'] ?? 'легко';

    // Валидация ENUM-значений — на всякий случай
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
                INSERT INTO recipes (title, description, cooking_time, ingredients, status, difficulty) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $description, $cooking_time, $ingredients, $status, $difficulty]);
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $error = "❌ Ошибка сохранения: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>➕ Добавить рецепт</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>➕ Добавить новый рецепт</h2>
        <a href="index.php" class="btn btn-secondary">⬅️ Назад</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Название рецепта <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Ингредиенты <span class="text-danger">*</span></label>
            <textarea name="ingredients" class="form-control" rows="6" required placeholder="Каждый ингредиент — с новой строки. Например:&#10;2 яйца&#10;100 г сахара&#10;1 ст.л. ванилина"><?= htmlspecialchars($_POST['ingredients'] ?? '') ?></textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Время приготовления (мин)</label>
                <input type="number" name="cooking_time" class="form-control" min="1" value="<?= htmlspecialchars($_POST['cooking_time'] ?? 30) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Сложность</label>
                <select name="difficulty" class="form-select">
                    <option value="легко" <?= (($_POST['difficulty'] ?? 'легко') == 'легко') ? 'selected' : '' ?>>Легко</option>
                    <option value="средне" <?= (($_POST['difficulty'] ?? 'легко') == 'средне') ? 'selected' : '' ?>>Средне</option>
                    <option value="сложно" <?= (($_POST['difficulty'] ?? 'легко') == 'сложно') ? 'selected' : '' ?>>Сложно</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="не приготовлен" <?= (($_POST['status'] ?? 'не приготовлен') == 'не приготовлен') ? 'selected' : '' ?>>Не приготовлен</option>
                    <option value="приготовлен" <?= (($_POST['status'] ?? 'не приготовлен') == 'приготовлен') ? 'selected' : '' ?>>Приготовлен</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Описание</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">💾 Сохранить рецепт</button>
        <a href="index.php" class="btn btn-secondary">Отмена</a>
    </form>
</div>
</body>
</html>