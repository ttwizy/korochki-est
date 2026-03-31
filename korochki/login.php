<?php
require_once __DIR__ . '/includes/auth.php';
requireGuest();

if (isPost()) {
    $login = cleanInput(isset($_POST['login']) ? $_POST['login'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    setOld($_POST);
    $errors = array();

    if ($login === '') {
        $errors[] = 'Введите логин.';
    }

    if ($password === '') {
        $errors[] = 'Введите пароль.';
    }

    if (!empty($errors)) {
        setErrors($errors);
        redirect('login.php');
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ? LIMIT 1");
    $stmt->execute(array($login));
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        setErrors(array('Неверный логин или пароль.'));
        redirect('login.php');
    }

    $_SESSION['user'] = array(
        'id' => $user['id'],
        'name' => $user['name'],
        'login' => $user['login'],
        'phone' => $user['phone'],
        'email' => $user['email'],
        'role' => $user['role']
    );

    clearOld();

    if ($user['role'] === 'admin') {
        setSuccess('Вы вошли как администратор.');
        redirect('admin/index.php');
    }

    setSuccess('Вы успешно вошли.');
    redirect('applications.php');
}

$pageTitle = 'Вход';
$metaDescription = 'Вход в систему портала Корочки.есть.';
$metaKeywords = 'вход, авторизация, корочки';

require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
    <div class="auth-shell reveal-on-scroll revealed">
        <div class="auth-card auth-card--small">
            <div class="auth-card__header">
                <h1 class="auth-title">Вход</h1>
                <p class="auth-text">Введите логин и пароль для доступа к системе.</p>
            </div>

            <form method="POST" action="login.php">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="login">Логин</label>
                        <input class="form-control" id="login" type="text" name="login" value="<?php echo old('login'); ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="password">Пароль</label>
                        <input class="form-control" id="password" type="password" name="password" required>
                    </div>

                    <div class="col-12 d-grid">
                        <button type="submit" class="btn btn-primary-soft">Войти</button>
                    </div>
                </div>
            </form>

            <div class="text-center mt-3 form-subtext">
                Ещё не зарегистрированы?
                <a href="register.php">Регистрация</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>