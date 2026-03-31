<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

if (!isPost()) {
    redirect('index.php');
}

$applicationId = isset($_POST['application_id']) ? (int)$_POST['application_id'] : 0;
$status = cleanInput(isset($_POST['status']) ? $_POST['status'] : '');

$allowedStatuses = array('Новая', 'Идет обучение', 'Обучение завершено');
$errors = array();

if ($applicationId <= 0) {
    $errors[] = 'Некорректная заявка.';
}

if (!in_array($status, $allowedStatuses, true)) {
    $errors[] = 'Выбран неверный статус.';
}

if (!empty($errors)) {
    setErrors($errors);
    redirect('index.php');
}

$stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
$stmt->execute(array($status, $applicationId));

setSuccess('Статус заявки обновлён.');
redirect('index.php');