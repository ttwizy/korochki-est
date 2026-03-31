<?php
require_once __DIR__ . '/includes/auth.php';
requireAuth();

if (isAdmin()) {
    redirect('admin/index.php');
}

$userId = (int)$_SESSION['user']['id'];

if (isPost()) {
    $action = isset($_POST['action']) ? cleanInput($_POST['action']) : '';

    if ($action === 'add_review') {
        $applicationId = isset($_POST['application_id']) ? (int)$_POST['application_id'] : 0;
        $reviewText = cleanInput(isset($_POST['review_text']) ? $_POST['review_text'] : '');

        $errors = array();

        if ($applicationId <= 0) {
            $errors[] = 'Некорректная заявка.';
        }

        if ($reviewText === '') {
            $errors[] = 'Введите текст отзыва.';
        } elseif (mb_strlen($reviewText, 'UTF-8') > 500) {
            $errors[] = 'Отзыв не должен превышать 500 символов.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("
                SELECT id, status, review_text
                FROM applications
                WHERE id = ? AND user_id = ?
                LIMIT 1
            ");
            $stmt->execute(array($applicationId, $userId));
            $applicationForReview = $stmt->fetch();

            if (!$applicationForReview) {
                $errors[] = 'Заявка не найдена.';
            } elseif ($applicationForReview['status'] !== 'Обучение завершено') {
                $errors[] = 'Отзыв можно оставить только после завершения обучения.';
            } elseif ($applicationForReview['review_text'] !== null && $applicationForReview['review_text'] !== '') {
                $errors[] = 'Отзыв уже оставлен.';
            }
        }

        if (!empty($errors)) {
            setErrors($errors);
            redirect('applications.php');
        }

        $stmt = $pdo->prepare("
            UPDATE applications
            SET review_text = ?, reviewed_at = NOW()
            WHERE id = ? AND user_id = ?
            LIMIT 1
        ");
        $stmt->execute(array($reviewText, $applicationId, $userId));

        setSuccess('Отзыв сохранён.');
        redirect('applications.php');
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

$perPage = 5;
$offset = ($page - 1) * $perPage;

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ?");
$countStmt->execute(array($userId));
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = (int)ceil($totalRows / $perPage);

$stmt = $pdo->prepare("
    SELECT a.*, c.title AS course_title
    FROM applications a
    INNER JOIN courses c ON c.id = a.course_id
    WHERE a.user_id = ?
    ORDER BY a.id DESC
    LIMIT " . (int)$perPage . " OFFSET " . (int)$offset
);
$stmt->execute(array($userId));
$applications = $stmt->fetchAll();

$pageTitle = 'Мои заявки';
$metaDescription = 'Просмотр заявок пользователя и отзывов.';
$metaKeywords = 'заявки, отзывы, обучение';

require_once __DIR__ . '/includes/header.php';
?>

<section class="reveal-on-scroll revealed">
    <div class="page-head mb-4">
        <div>
            <h1 class="section-title mb-2">Мои заявки</h1>
            <p class="section-text mb-0">Здесь отображаются отправленные заявки, статусы и отзывы.</p>
        </div>
        <div>
            <a href="create_application.php" class="btn btn-primary-soft">Новая заявка</a>
        </div>
    </div>

    <div class="table-shell">
        <div class="table-responsive">
            <table class="table align-middle mb-0 site-table">
                <thead>
                    <tr>
                        <th>Курс</th>
                        <th>Дата начала</th>
                        <th>Оплата</th>
                        <th>Статус</th>
                        <th>Комментарий</th>
                        <th>Отзыв</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td><?php echo e($application['course_title']); ?></td>
                                <td><?php echo e(formatDateRu($application['start_date'])); ?></td>
                                <td><?php echo e($application['payment_method']); ?></td>
                                <td>
                                    <span class="<?php echo getStatusClass($application['status']); ?>">
                                        <?php echo e($application['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $application['comment'] !== '' ? e($application['comment']) : '—'; ?></td>
                                <td style="min-width:260px;">
                                    <?php if ($application['status'] === 'Обучение завершено' && ($application['review_text'] === null || $application['review_text'] === '')): ?>
                                        <form action="applications.php" method="POST">
                                            <input type="hidden" name="action" value="add_review">
                                            <input type="hidden" name="application_id" value="<?php echo (int)$application['id']; ?>">
                                            <textarea class="form-control mb-2" name="review_text" rows="3" placeholder="Оставьте отзыв" required></textarea>
                                            <button type="submit" class="btn btn-primary-soft btn-sm">Сохранить отзыв</button>
                                        </form>
                                    <?php elseif ($application['review_text'] !== null && $application['review_text'] !== ''): ?>
                                        <div class="review-inline">
                                            <?php echo nl2br(e($application['review_text'])); ?>
                                        </div>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">У вас пока нет заявок.</td>
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
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Назад</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Вперед</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>