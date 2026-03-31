<?php
require_once __DIR__ . '/includes/auth.php';
requireAuth();

if (isAdmin()) {
    redirect('admin/index.php');
}

if (isPost()) {
    $courseId = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $startDateRu = cleanInput(isset($_POST['start_date']) ? $_POST['start_date'] : '');
    $paymentMethod = cleanInput(isset($_POST['payment_method']) ? $_POST['payment_method'] : '');
    $comment = cleanInput(isset($_POST['comment']) ? $_POST['comment'] : '');

    setOld($_POST);

    $errors = array();
    $allowedPaymentMethods = array('Наличными', 'Перевод по номеру телефона');

    if ($courseId <= 0) {
        $errors[] = 'Выберите курс.';
    }

    if ($startDateRu === '') {
        $errors[] = 'Укажите желаемую дату начала.';
    } else {
        $startDateSql = parseDateRuToSql($startDateRu);
        if ($startDateSql === false) {
            $errors[] = 'Дата должна быть в формате ДД.ММ.ГГГГ.';
        } elseif ($startDateSql < date('Y-m-d')) {
            $errors[] = 'Дата начала не может быть раньше сегодняшнего дня.';
        }
    }

    if ($paymentMethod === '' || !in_array($paymentMethod, $allowedPaymentMethods, true)) {
        $errors[] = 'Выберите корректный способ оплаты.';
    }

    if (strlen($comment) > 500) {
        $errors[] = 'Комментарий не должен превышать 500 символов.';
    }

    if (!empty($errors)) {
        setErrors($errors);
        redirect('create_application.php');
    }

    $stmt = $pdo->prepare("
        INSERT INTO applications (user_id, course_id, start_date, payment_method, status, comment, review_text, reviewed_at)
        VALUES (?, ?, ?, ?, 'Новая', ?, NULL, NULL)
    ");
    $stmt->execute(array($_SESSION['user']['id'], $courseId, $startDateSql, $paymentMethod, $comment));

    clearOld();
    setSuccess('Заявка успешно отправлена.');
    redirect('applications.php');
}

$stmt = $pdo->query("SELECT * FROM courses ORDER BY id ASC");
$courses = $stmt->fetchAll();

$pageTitle = 'Новая заявка';
$metaDescription = 'Форма подачи заявки на обучение.';
$metaKeywords = 'заявка, курс, обучение';

require_once __DIR__ . '/includes/header.php';
?>

<section class="reveal-on-scroll revealed">
    <div class="page-head mb-4">
        <div>
            <h1 class="section-title mb-2">Оформить заявку</h1>
            <p class="section-text mb-0">Выберите курс, дату начала и способ оплаты.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="form-panel h-100">
                <form method="POST" action="create_application.php">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="course_id">Название курса</label>
                            <select class="form-select" name="course_id" id="course_id" required>
                                <option value="">Выберите курс</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo (int)$course['id']; ?>" <?php echo old('course_id') == $course['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($course['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="start_date">Желаемая дата начала</label>
                            <input class="form-control" type="text" name="start_date" id="start_date" placeholder="25.03.2026" value="<?php echo old('start_date'); ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="payment_method">Способ оплаты</label>
                            <select class="form-select" name="payment_method" id="payment_method" required>
                                <option value="">Выберите способ оплаты</option>
                                <option value="Наличными" <?php echo old('payment_method') === 'Наличными' ? 'selected' : ''; ?>>Наличными</option>
                                <option value="Перевод по номеру телефона" <?php echo old('payment_method') === 'Перевод по номеру телефона' ? 'selected' : ''; ?>>Перевод по номеру телефона</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="comment">Комментарий</label>
                            <textarea class="form-control" name="comment" id="comment" rows="5"><?php echo old('comment'); ?></textarea>
                        </div>

                        <div class="col-12 d-grid d-md-block">
                            <button type="submit" class="btn btn-primary-soft">Отправить заявку</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="side-info h-100">
                <h2 class="h5 mb-3">Перед отправкой проверьте</h2>
                <ul class="side-info__list">
                    <li>правильно ли выбран курс;</li>
                    <li>указана ли дата в формате ДД.ММ.ГГГГ;</li>
                    <li>выбран ли способ оплаты;</li>
                    <li>не превышает ли комментарий 500 символов.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>