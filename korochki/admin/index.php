<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

$search = cleanInput(isset($_GET['search']) ? $_GET['search'] : '');
$statusFilter = cleanInput(isset($_GET['status']) ? $_GET['status'] : '');

$perPage = 7;
$offset = ($page - 1) * $perPage;

$where = array();
$params = array();

if ($search !== '') {
    $where[] = "(u.name LIKE ? OR u.login LIKE ? OR u.phone LIKE ? OR u.email LIKE ? OR c.title LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($statusFilter !== '') {
    $where[] = "a.status = ?";
    $params[] = $statusFilter;
}

$whereSql = '';
if (!empty($where)) {
    $whereSql = ' WHERE ' . implode(' AND ', $where);
}

$countSql = "
    SELECT COUNT(*)
    FROM applications a
    INNER JOIN users u ON u.id = a.user_id
    INNER JOIN courses c ON c.id = a.course_id
    " . $whereSql;

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = (int)ceil($totalRows / $perPage);

$listSql = "
    SELECT
        a.*,
        u.name AS user_name,
        u.login AS user_login,
        u.phone AS user_phone,
        u.email AS user_email,
        c.title AS course_title
    FROM applications a
    INNER JOIN users u ON u.id = a.user_id
    INNER JOIN courses c ON c.id = a.course_id
    " . $whereSql . "
    ORDER BY a.id DESC
    LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

$listStmt = $pdo->prepare($listSql);
$listStmt->execute($params);
$applications = $listStmt->fetchAll();

$statAll = (int)$pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$statNew = (int)$pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'Новая'")->fetchColumn();
$statProgress = (int)$pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'Идет обучение'")->fetchColumn();
$statDone = (int)$pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'Обучение завершено'")->fetchColumn();

$pageTitle = 'Админ-панель';
$metaDescription = 'Панель администратора для управления заявками.';
$metaKeywords = 'админ, заявки, статусы';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="reveal-on-scroll revealed">
    <div class="page-head mb-4">
        <div>
            <h1 class="section-title mb-2">Панель администратора</h1>
            <p class="section-text mb-0">Просматривайте заявки, фильтруйте данные и меняйте статусы обучения.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="dashboard-stat h-100">
                <div class="dashboard-stat__label">Всего заявок</div>
                <div class="dashboard-stat__value"><?php echo $statAll; ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dashboard-stat h-100">
                <div class="dashboard-stat__label">Новые</div>
                <div class="dashboard-stat__value"><?php echo $statNew; ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dashboard-stat h-100">
                <div class="dashboard-stat__label">Идет обучение</div>
                <div class="dashboard-stat__value"><?php echo $statProgress; ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dashboard-stat h-100">
                <div class="dashboard-stat__label">Завершено</div>
                <div class="dashboard-stat__value"><?php echo $statDone; ?></div>
            </div>
        </div>
    </div>

    <div class="filter-panel mb-4">
        <form method="GET" action="index.php">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6 col-lg-5">
                    <label class="form-label" for="search">Поиск</label>
                    <input class="form-control" type="text" name="search" id="search" value="<?php echo e($search); ?>" placeholder="ФИО, логин, телефон, email, курс">
                </div>

                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label" for="status">Статус</label>
                    <select class="form-select" name="status" id="status">
                        <option value="">Все статусы</option>
                        <option value="Новая" <?php echo $statusFilter === 'Новая' ? 'selected' : ''; ?>>Новая</option>
                        <option value="Идет обучение" <?php echo $statusFilter === 'Идет обучение' ? 'selected' : ''; ?>>Идет обучение</option>
                        <option value="Обучение завершено" <?php echo $statusFilter === 'Обучение завершено' ? 'selected' : ''; ?>>Обучение завершено</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-primary-soft">Применить</button>
                </div>

                <div class="col-12 col-lg-2 d-grid">
                    <a href="index.php" class="btn btn-outline-soft">Сбросить</a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-shell">
        <div class="table-responsive">
            <table class="table align-middle mb-0 site-table">
                <thead>
                    <tr>
                        <th>ФИО</th>
                        <th>Логин</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Курс</th>
                        <th>Дата</th>
                        <th>Оплата</th>
                        <th>Статус</th>
                        <th>Отзыв</th>
                        <th>Изменить</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td><?php echo e($application['user_name']); ?></td>
                                <td><?php echo e($application['user_login']); ?></td>
                                <td><?php echo e($application['user_phone']); ?></td>
                                <td><?php echo e($application['user_email']); ?></td>
                                <td><?php echo e($application['course_title']); ?></td>
                                <td><?php echo e(formatDateRu($application['start_date'])); ?></td>
                                <td><?php echo e($application['payment_method']); ?></td>
                                <td>
                                    <span class="<?php echo getStatusClass($application['status']); ?>">
                                        <?php echo e($application['status']); ?>
                                    </span>
                                </td>
                                <td style="min-width:220px;">
                                    <?php echo ($application['review_text'] !== null && $application['review_text'] !== '') ? nl2br(e($application['review_text'])) : '—'; ?>
                                </td>
                                <td style="min-width:200px;">
                                    <form action="update_status.php" method="POST" class="d-grid gap-2">
                                        <input type="hidden" name="application_id" value="<?php echo (int)$application['id']; ?>">
                                        <select class="form-select form-select-sm" name="status" required>
                                            <option value="Новая" <?php echo $application['status'] === 'Новая' ? 'selected' : ''; ?>>Новая</option>
                                            <option value="Идет обучение" <?php echo $application['status'] === 'Идет обучение' ? 'selected' : ''; ?>>Идет обучение</option>
                                            <option value="Обучение завершено" <?php echo $application['status'] === 'Обучение завершено' ? 'selected' : ''; ?>>Обучение завершено</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary-soft btn-sm">Сохранить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">Заявки не найдены.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination flex-wrap">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>">Назад</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>">Вперед</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>