<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>📚 Каталог рецептов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-recipe { transition: transform 0.2s; }
        .card-recipe:hover { transform: translateY(-5px); }
        .status-cooked { background-color: #e8f5e9; border-left: 4px solid #28a745; }
        .status-not-cooked { background-color: #fff3cd; border-left: 4px solid #ffc107; }
    </style>
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
                $diffClass = match($row['difficulty']) {
                    'легко' => 'bg-success',
                    'средне' => 'bg-warning text-dark',
                    'сложно' => 'bg-danger',
                    default => 'bg-secondary'
                };
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm card-recipe <?= $cardClass ?>">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($row['title']) ?></h5>
                                <span class="badge <?= $diffClass ?>"><?= htmlspecialchars($row['difficulty']) ?></span>
                            </div>

                            <?php if (!empty($row['description'])): ?>
                                <p class="card-text small text-muted mb-2"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                            <?php endif; ?>

                            <p class="card-text small">
                                <strong>⏱️ Время:</strong> <?= (int)$row['cooking_time'] ?> мин.<br>
                                <strong>📅 Добавлено:</strong> <?= htmlspecialchars($row['created_at']) ?>
                            </p>

                            <p class="card-text">
                                <strong>📋 Ингредиенты:</strong>
                                <ul class="mb-0">
                                    <?php
                                    $ingredients = explode("\n", $row['ingredients']);
                                    foreach ($ingredients as $ing) {
                                        $ing = trim($ing);
                                        if ($ing) echo "<li>" . htmlspecialchars($ing) . "</li>";
                                    }
                                    ?>
                                </ul>
                            </p>

                            <div class="mt-auto pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge <?= $row['status'] === 'приготовлен' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                        <?= $row['status'] === 'приготовлен' ? '✅ Приготовлен' : '⏳ Не приготовлен' ?>
                                    </span>
                                    <div>
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">✏️</a>
                                        <a href="update_status.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-<?= $row['status'] === 'приготовлен' ? 'warning' : 'success' ?>">
                                            <?= $row['status'] === 'приготовлен' ? '🔁 Сбросить' : '✅ Готовил' ?>
                                        </a>
                                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Точно удалить рецепт?')">🗑️</a>
                                    </div>
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