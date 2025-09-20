<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📚 Каталог рецептов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>📖 Каталог рецептов</h1>
            <a href="add.php" class="btn btn-success">➕ Добавить рецепт</a>
        </div>

        <?php
        require_once 'config.php';

        try {
            $stmt = $pdo->query("SELECT * FROM recipes ORDER BY created_at DESC");
            $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($recipes) == 0) {
                echo '<div class="alert alert-info">📭 Пока нет ни одного рецепта. Добавьте первый!</div>';
            } else {
                echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';
                foreach ($recipes as $row) {
                    $cardClass = $row['status'] === 'приготовлен' ? 'status-cooked' : 'status-not-cooked';
                    $diffClass = match ($row['difficulty']) {
                        'легко' => 'bg-success',
                        'средне' => 'bg-warning text-dark',
                        'сложно' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    ?>
                    <div class="col">
                        <div class="card shadow-sm card-recipe <?= $cardClass ?>">
                            <div class="card-body">
                                <!-- Заголовок -->
                                <div class="card-title-block d-flex justify-content-between align-items-start">
                                    <h5 class="card-title mb-0"><?= htmlspecialchars($row['title']) ?></h5>
                                    <span class="badge <?= $diffClass ?>"><?= htmlspecialchars($row['difficulty']) ?></span>
                                </div>

                                <!-- Ингредиенты -->
                                <div class="ingredients-list">
                                    <strong>📋 Ингредиенты:</strong>
                                    <ul class="mb-0 small">
                                        <?php
                                        $ingredients = explode("\n", $row['ingredients']);
                                        foreach ($ingredients as $ing) {
                                            $ing = trim($ing);
                                            if ($ing) {
                                                echo "<li>" . htmlspecialchars($ing) . "</li>";
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>

                                <!-- Описание -->
                                <?php if (!empty($row['description'])):
                                    $desc = htmlspecialchars($row['description']);
                                    $maxLen = 400; // Можно показать больше, так как карточка большая
                                    $shortDesc = mb_strlen($desc) > $maxLen ? mb_substr($desc, 0, $maxLen) . '...' : $desc;
                                    ?>
                                    <div class="short-desc">
                                        <strong>📝 Описание:</strong><br>
                                        <?= nl2br($shortDesc) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="short-desc text-muted">
                                        <strong>📝 Описание:</strong> отсутствует
                                    </div>
                                <?php endif; ?>

                                <!-- Кнопки -->
                                <div class="card-footer-actions">
                                    <span class="badge <?= $row['status'] === 'приготовлен' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                        <?= $row['status'] === 'приготовлен' ? '✅ Приготовлен' : '⏳ Не приготовлен' ?>
                                    </span>
                                    <div>
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">✏️</a>
                                        <a href="update_status.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-<?= $row['status'] === 'приготовлен' ? 'warning' : 'success' ?>">
                                            <?= $row['status'] === 'приготовлен' ? '❌ Не готовил' : '✅ Готовил' ?>
                                        </a>
                                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Точно удалить рецепт?')">🗑️</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                echo '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Ошибка загрузки рецептов: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
</body>

</html>