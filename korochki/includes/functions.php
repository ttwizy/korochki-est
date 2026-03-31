<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function isPost()
{
    return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
}

function isLoggedIn()
{
    return !empty($_SESSION['user']);
}

function isAdmin()
{
    return isLoggedIn()
        && isset($_SESSION['user']['role'])
        && $_SESSION['user']['role'] === 'admin';
}

function requireGuest()
{
    if (isLoggedIn()) {
        if (isAdmin()) {
            redirect('admin/index.php');
        }
        redirect('applications.php');
    }
}

function requireAuth()
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireAdmin()
{
    if (!isAdmin()) {
        http_response_code(403);
        die('Доступ запрещён.');
    }
}

function old($key, $default = '')
{
    if (isset($_SESSION['old']) && isset($_SESSION['old'][$key])) {
        return e($_SESSION['old'][$key]);
    }

    return e($default);
}

function setOld($data)
{
    $_SESSION['old'] = $data;
}

function clearOld()
{
    unset($_SESSION['old']);
}

function setErrors($errors)
{
    $_SESSION['errors'] = $errors;
}

function getErrors()
{
    $errors = array();

    if (isset($_SESSION['errors'])) {
        $errors = $_SESSION['errors'];
        unset($_SESSION['errors']);
    }

    return $errors;
}

function setSuccess($message)
{
    $_SESSION['success'] = $message;
}

function getSuccess()
{
    $message = null;

    if (isset($_SESSION['success'])) {
        $message = $_SESSION['success'];
        unset($_SESSION['success']);
    }

    return $message;
}

function cleanInput($value)
{
    return trim(strip_tags($value));
}

function validateName($name)
{
    return preg_match('/^[А-Яа-яЁё\s\-]+$/u', $name) === 1;
}

function validateLogin($login)
{
    if ($login === 'Admin') {
        return true;
    }

    return preg_match('/^[A-Za-z0-9]{6,}$/', $login) === 1;
}

function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function normalizePhone($phone)
{
    $digits = preg_replace('/\D+/', '', $phone);

    if (strlen($digits) === 11 && $digits[0] === '7') {
        $digits = '8' . substr($digits, 1);
    }

    if (strlen($digits) !== 11 || $digits[0] !== '8') {
        return false;
    }

    return sprintf(
        '8(%s)%s-%s-%s',
        substr($digits, 1, 3),
        substr($digits, 4, 3),
        substr($digits, 7, 2),
        substr($digits, 9, 2)
    );
}

function validatePhone($phone)
{
    return normalizePhone($phone) !== false;
}

function parseDateRuToSql($date)
{
    $date = trim($date);

    if (!preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $date, $matches)) {
        return false;
    }

    $day = (int)$matches[1];
    $month = (int)$matches[2];
    $year = (int)$matches[3];

    if (!checkdate($month, $day, $year)) {
        return false;
    }

    return sprintf('%04d-%02d-%02d', $year, $month, $day);
}

function formatDateRu($dateSql)
{
    if (!$dateSql) {
        return '';
    }

    $timestamp = strtotime($dateSql);

    if ($timestamp === false) {
        return '';
    }

    return date('d.m.Y', $timestamp);
}

function getStatusClass($status)
{
    if ($status === 'Новая') {
        return 'status-badge status-new';
    }

    if ($status === 'Идет обучение') {
        return 'status-badge status-progress';
    }

    if ($status === 'Обучение завершено') {
        return 'status-badge status-done';
    }

    return 'status-badge';
}

function getCurrentScheme()
{
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    if (!$isHttps && isset($_SERVER['SERVER_PORT']) && (string)$_SERVER['SERVER_PORT'] === '443') {
        $isHttps = true;
    }

    return $isHttps ? 'https' : 'http';
}

function normalizeBaseUrl($url)
{
    $url = trim((string)$url);

    if ($url === '') {
        return '';
    }

    return rtrim($url, '/') . '/';
}

function getConfiguredSiteUrl()
{
    if (defined('SITE_URL')) {
        return normalizeBaseUrl(SITE_URL);
    }

    return '';
}

function getSiteUrl()
{
    $configured = getConfiguredSiteUrl();
    if ($configured !== '') {
        return $configured;
    }

    $scheme = getCurrentScheme();
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
    $directory = trim(str_replace(basename($scriptName), '', $scriptName), '/');

    if ($directory !== '') {
        return $scheme . '://' . $host . '/' . $directory . '/';
    }

    return $scheme . '://' . $host . '/';
}

function pathUrl($path)
{
    $path = ltrim((string)$path, '/');
    return getSiteUrl() . $path;
}

function currentUrl()
{
    $requestUri = isset($_SERVER['REQUEST_URI']) ? ltrim($_SERVER['REQUEST_URI'], '/') : '';
    return getSiteUrl() . $requestUri;
}