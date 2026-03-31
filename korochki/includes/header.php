<?php
require_once __DIR__ . '/functions.php';

$errors = getErrors();
$successMessage = getSuccess();

if (!isset($pageTitle)) {
    $pageTitle = 'Корочки.есть';
}

if (!isset($metaDescription)) {
    $metaDescription = 'Портал записи на онлайн-курсы дополнительного профессионального образования.';
}

if (!isset($metaKeywords)) {
    $metaKeywords = 'корочки, курсы, обучение, заявки';
}

$isAdminArea = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePrefix = $isAdminArea ? '../' : '';

if (!isset($ogImage)) {
    $ogImage = $basePrefix . 'media/image02.jpg';
}

$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://'
    . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost')
    . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/');
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?></title>
    <meta name="description" content="<?php echo e($metaDescription); ?>">
    <meta name="keywords" content="<?php echo e($metaKeywords); ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($pageTitle); ?>">
    <meta property="og:description" content="<?php echo e($metaDescription); ?>">
    <meta property="og:url" content="<?php echo e($currentUrl); ?>">
    <meta property="og:image" content="<?php echo e($ogImage); ?>">
    <meta property="og:locale" content="ru_RU">
    <meta name="twitter:card" content="summary_large_image">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $basePrefix; ?>assets/css/style.css" rel="stylesheet">
</head>
<body class="site-body">

<div class="bg-decor bg-decor--one"></div>
<div class="bg-decor bg-decor--two"></div>

<header class="site-header">
    <div class="container">
        <nav class="navbar navbar-expand-lg site-navbar p-0">
            <a class="navbar-brand site-logo" href="<?php echo $basePrefix; ?>index.php">
                <span class="site-logo__mark">К</span>
                <span class="site-logo__text">Корочки.есть</span>
            </a>

            <button class="navbar-toggler border-0 shadow-none p-0 site-burger" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Открыть меню">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto align-items-lg-center site-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePrefix; ?>index.php">Главная</a>
                    </li>

                    <?php if (!isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePrefix; ?>register.php">Регистрация</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary-soft btn-sm ms-lg-2 mt-2 mt-lg-0" href="<?php echo $basePrefix; ?>login.php">Войти</a>
                        </li>
                    <?php else: ?>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $basePrefix; ?>admin/index.php">Админ-панель</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $basePrefix; ?>create_application.php">Новая заявка</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $basePrefix; ?>applications.php">Мои заявки</a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <span class="nav-link site-user-name"><?php echo e($_SESSION['user']['name']); ?></span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="<?php echo $basePrefix; ?>logout.php" class="logout-form ms-lg-2 mt-2 mt-lg-0">
                                <button type="submit" class="btn btn-outline-soft btn-sm w-100">Выход</button>
                            </form>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</header>

<main class="site-main">
    <div class="container site-content-shell">

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger app-alert mb-3">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div class="alert alert-success app-alert mb-3">
                <?php echo e($successMessage); ?>
            </div>
        <?php endif; ?>