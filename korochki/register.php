<?php
require_once __DIR__ . '/includes/auth.php';
requireGuest();

if (isPost()) {
    $name = cleanInput(isset($_POST['name']) ? $_POST['name'] : '');
    $login = cleanInput(isset($_POST['login']) ? $_POST['login'] : '');
    $phone = cleanInput(isset($_POST['phone']) ? $_POST['phone'] : '');
    $email = cleanInput(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $passwordConfirmation = isset($_POST['password_confirmation']) ? $_POST['password_confirmation'] : '';

    setOld($_POST);
    $errors = array();

    if ($name === '') {
        $errors[] = 'Введите ФИО.';
    } elseif (!validateName($name)) {
        $errors[] = 'ФИО должно содержать только кириллицу, пробелы и дефис.';
    }

    if ($login === '') {
        $errors[] = 'Введите логин.';
    } elseif (!validateLogin($login) || $login === 'Admin') {
        $errors[] = 'Логин должен содержать только латинские буквы и цифры, не менее 6 символов.';
    }

    if ($phone === '') {
        $errors[] = 'Введите телефон.';
    } elseif (!validatePhone($phone)) {
        $errors[] = 'Телефон должен быть в формате 8(XXX)XXX-XX-XX.';
    } else {
        $phone = normalizePhone($phone);
    }

    if ($email === '') {
        $errors[] = 'Введите email.';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Введите корректный email.';
    }

    if ($password === '') {
        $errors[] = 'Введите пароль.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Пароль должен содержать не менее 8 символов.';
    }

    if ($passwordConfirmation === '') {
        $errors[] = 'Подтвердите пароль.';
    } elseif ($password !== $passwordConfirmation) {
        $errors[] = 'Пароли не совпадают.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ? LIMIT 1");
        $stmt->execute(array($login));
        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким логином уже существует.';
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
        $stmt->execute(array($phone));
        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким телефоном уже существует.';
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute(array($email));
        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким email уже существует.';
        }
    }

    if (!empty($errors)) {
        setErrors($errors);
        redirect('register.php');
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (name, login, phone, email, password, role)
        VALUES (?, ?, ?, ?, ?, 'user')
    ");
    $stmt->execute(array($name, $login, $phone, $email, $hashedPassword));

    $_SESSION['user'] = array(
        'id' => $pdo->lastInsertId(),
        'name' => $name,
        'login' => $login,
        'phone' => $phone,
        'email' => $email,
        'role' => 'user'
    );

    clearOld();
    setSuccess('Регистрация успешно завершена.');
    redirect('applications.php');
}

$pageTitle = 'Регистрация';
$metaDescription = 'Регистрация нового пользователя на портале Корочки.есть.';
$metaKeywords = 'регистрация, пользователь, корочки';

require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
    <div class="auth-shell reveal-on-scroll revealed">
        <div class="auth-card">
            <div class="auth-card__header">
                <h1 class="auth-title">Регистрация</h1>
                <p class="auth-text">Заполните поля для создания пользователя в системе.</p>
            </div>

            <form method="POST" action="register.php">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="name">ФИО</label>
                        <input class="form-control" id="name" type="text" name="name" value="<?php echo old('name'); ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="login">Логин</label>
                        <input class="form-control" id="login" type="text" name="login" value="<?php echo old('login'); ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="phone">Телефон</label>
                        <input class="form-control" id="phone" type="text" name="phone" value="<?php echo old('phone'); ?>" placeholder="8(900)123-45-67" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" id="email" type="email" name="email" value="<?php echo old('email'); ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="password">Пароль</label>
                        <input class="form-control" id="password" type="password" name="password" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="password_confirmation">Подтверждение пароля</label>
                        <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required>
                    </div>

                    <div class="col-12 d-grid">
                        <button type="submit" class="btn btn-primary-soft">Зарегистрироваться</button>
                    </div>
                </div>
            </form>

            <div class="text-center mt-3 form-subtext">
                Уже зарегистрированы?
                <a href="login.php">Авторизация</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>