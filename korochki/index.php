<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Корочки.есть — онлайн-курсы дополнительного профессионального образования';
$metaDescription = 'Корочки.есть — портал записи на онлайн-курсы дополнительного профессионального образования с регистрацией, подачей заявок, личным кабинетом и админ-панелью.';
$metaKeywords = 'Корочки.есть, онлайн-курсы, дополнительное профессиональное образование, запись на обучение, портал курсов, админ-панель, отзывы, веб-приложение';
$canonicalUrl = pathUrl('index.php');
$ogImage = pathUrl('media/image02.jpg');

$stmt = $pdo->query("SELECT * FROM courses ORDER BY id ASC LIMIT 3");
$courses = $stmt->fetchAll();

$reviewStmt = $pdo->query("
    SELECT a.review_text, a.reviewed_at, u.name AS user_name, c.title AS course_title
    FROM applications a
    INNER JOIN users u ON u.id = a.user_id
    INNER JOIN courses c ON c.id = a.course_id
    WHERE a.status = 'Обучение завершено'
      AND a.review_text IS NOT NULL
      AND a.review_text <> ''
    ORDER BY a.id DESC
    LIMIT 3
");
$reviews = $reviewStmt->fetchAll();

$courseElements = array();
foreach ($courses as $course) {
    $courseElements[] = array(
        '@type' => 'Course',
        'name' => $course['title'],
        'description' => $course['description'],
        'provider' => array(
            '@type' => 'Organization',
            'name' => APP_NAME,
            'url' => getSiteUrl()
        )
    );
}

$pageStructuredData = array(
    '@context' => 'https://schema.org',
    '@graph' => array(
        array(
            '@type' => 'WebSite',
            'name' => APP_NAME,
            'url' => getSiteUrl(),
            'description' => $metaDescription,
            'inLanguage' => 'ru-RU'
        ),
        array(
            '@type' => 'Organization',
            'name' => APP_NAME,
            'url' => getSiteUrl(),
            'logo' => $ogImage,
            'description' => 'Портал записи на онлайн-курсы дополнительного профессионального образования.'
        ),
        array(
            '@type' => 'ItemList',
            'name' => 'Доступные курсы',
            'itemListElement' => $courseElements
        )
    )
);

require_once __DIR__ . '/includes/header.php';
?>

<section class="mb-4 mb-lg-5">
    <div class="row g-4 align-items-stretch">
        <div class="col-12 col-lg-6">
            <div class="hero-box hero-box--main h-100 fade-in-up">
                <span class="hero-badge">Онлайн-портал заявок</span>
                <h1 class="hero-title">Корочки.есть</h1>
                <p class="hero-text">
                    Современный сервис для записи на курсы дополнительного профессионального образования.
                    Пользователь может зарегистрироваться, войти по логину, подать заявку, отслеживать статус
                    и оставить отзыв после завершения обучения.
                </p>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-brand">Зарегистрироваться</a>
                        <a href="login.php" class="btn btn-soft">Войти</a>
                    <?php else: ?>
                        <?php if (isAdmin()): ?>
                            <a href="admin/index.php" class="btn btn-brand">Открыть админ-панель</a>
                        <?php else: ?>
                            <a href="create_application.php" class="btn btn-brand">Подать заявку</a>
                            <a href="applications.php" class="btn btn-soft">Мои заявки</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-6 col-md-4">
                        <div class="mini-stat h-100">
                            <div class="mini-stat__value">3</div>
                            <div class="mini-stat__label">основных курса</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="mini-stat h-100">
                            <div class="mini-stat__value">24/7</div>
                            <div class="mini-stat__label">доступ к заявкам</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="mini-stat h-100">
                            <div class="mini-stat__value">PHP 7.1</div>
                            <div class="mini-stat__label">совместимость</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="simple-slider fade-in-up" data-simple-slider>
                <div class="simple-slide active" style="background-image:url('media/image02.jpg');">
                    <div class="simple-slide-content">
                        <span class="slide-chip">Слайд 1</span>
                        <h2 class="h3">Современные программы обучения</h2>
                        <p>Выберите подходящий курс и отправьте заявку через удобную форму.</p>
                    </div>
                </div>

                <div class="simple-slide" style="background-image:url('media/image04.jpg');">
                    <div class="simple-slide-content">
                        <span class="slide-chip">Слайд 2</span>
                        <h2 class="h3">Личный кабинет пользователя</h2>
                        <p>Все заявки, даты, способы оплаты и статусы собраны в одном месте.</p>
                    </div>
                </div>

                <div class="simple-slide" style="background-image:url('media/image11.jpg');">
                    <div class="simple-slide-content">
                        <span class="slide-chip">Слайд 3</span>
                        <h2 class="h3">Удобная панель администратора</h2>
                        <p>Фильтрация, поиск и изменение статусов реализованы в понятном виде.</p>
                    </div>
                </div>

                <div class="simple-slide" style="background-image:url('media/image15.jpg');">
                    <div class="simple-slide-content">
                        <span class="slide-chip">Слайд 4</span>
                        <h2 class="h3">Отзывы после завершения курса</h2>
                        <p>После завершения обучения пользователь может оставить отзыв о качестве услуг.</p>
                    </div>
                </div>

                <button class="slider-btn slider-btn-prev" type="button" data-slider-prev>‹</button>
                <button class="slider-btn slider-btn-next" type="button" data-slider-next>›</button>
            </div>
        </div>
    </div>
</section>

<section class="mb-4 mb-lg-5 reveal-on-scroll">
    <div class="section-head">
        <div>
            <div class="section-kicker">Преимущества</div>
            <h2 class="section-title">Почему сайтом удобно пользоваться</h2>
            <p class="section-text mb-0">Интерфейс упрощает основные действия пользователя и администратора.</p>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12 col-md-6 col-xl-4">
            <div class="content-card feature-card h-100">
                <div class="feature-number">01</div>
                <h3 class="h5">Регистрация по требованиям</h3>
                <p class="mb-0">ФИО, логин, телефон, email и пароль валидируются по правилам из методички.</p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="content-card feature-card h-100">
                <div class="feature-number">02</div>
                <h3 class="h5">Понятная подача заявки</h3>
                <p class="mb-0">Курс, дата начала, способ оплаты и комментарий оформляются в одной форме.</p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="content-card feature-card h-100">
                <div class="feature-number">03</div>
                <h3 class="h5">Отзывы после завершения</h3>
                <p class="mb-0">После статуса «Обучение завершено» пользователь получает доступ к отзыву.</p>
            </div>
        </div>
    </div>
</section>

<section class="mb-4 mb-lg-5 reveal-on-scroll">
    <div class="promo-panel">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-lg-7">
                <div class="section-kicker">О проекте</div>
                <h2 class="section-title">Сайт приведён к требованиям практики</h2>
                <p class="section-text mb-0">
                    Реализованы регистрация, авторизация, личный кабинет, создание заявок, административная часть,
                    адаптация под мобильные устройства, слайдер, отзывы и аккуратный современный интерфейс.
                </p>
            </div>
            <div class="col-12 col-lg-5">
                <div class="promo-list">
                    <div class="promo-list__item">Авторизация по логину и паролю</div>
                    <div class="promo-list__item">Адаптивная мобильная и планшетная версия</div>
                    <div class="promo-list__item">Плавные анимации и улучшенный дизайн</div>
                    <div class="promo-list__item">Отзывы после завершения обучения</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-4 mb-lg-5 reveal-on-scroll">
    <div class="section-head">
        <div>
            <div class="section-kicker">SEO и продвижение</div>
            <h2 class="section-title">Что уже реализовано для продвижения сайта</h2>
            <p class="section-text mb-0">На главной странице добавлены элементы, которые улучшают индексацию и оформление репоста в соцсетях.</p>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="content-card h-100">
                <h3 class="h5">Мета-теги</h3>
                <p class="mb-0">Для страниц заданы корректные title, description, keywords, robots и canonical.</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="content-card h-100">
                <h3 class="h5">Репост в соцсети</h3>
                <p class="mb-0">Добавлены Open Graph и Twitter Card, чтобы ссылка корректно отображалась при отправке.</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="content-card h-100">
                <h3 class="h5">Микроразметка</h3>
                <p class="mb-0">На странице подключена JSON-LD микроразметка для сайта, организации и списка курсов.</p>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="content-card h-100">
                <h3 class="h5">Готовность к хостингу</h3>
                <p class="mb-0">Вынесен базовый адрес сайта в конфиг, поэтому после публикации ссылки и соцтеги легко обновляются.</p>
            </div>
        </div>
    </div>
</section>

<section class="mb-4 mb-lg-5 reveal-on-scroll">
    <div class="section-head">
        <div>
            <div class="section-kicker">Программы</div>
            <h2 class="section-title">Доступные курсы</h2>
            <p class="section-text mb-0">Основные направления обучения, соответствующие заданию.</p>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <?php foreach ($courses as $course): ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="course-card h-100">
                    <div class="course-card__top">
                        <span class="course-badge">Программа</span>
                    </div>
                    <h3 class="h5"><?php echo e($course['title']); ?></h3>
                    <p><?php echo e($course['description']); ?></p>
                    <div class="course-meta">
                        <div>Длительность: <?php echo e($course['duration']); ?></div>
                        <div>Цена: <?php echo number_format((float)$course['price'], 0, ',', ' '); ?> ₽</div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="mb-4 reveal-on-scroll">
    <div class="section-head">
        <div>
            <div class="section-kicker">Мнения пользователей</div>
            <h2 class="section-title">Отзывы выпускников</h2>
            <p class="section-text mb-0">Отзывы отображаются после завершения обучения.</p>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="review-card h-100">
                        <div class="review-card__head">
                            <span class="review-course"><?php echo e($review['course_title']); ?></span>
                        </div>
                        <h3 class="h5"><?php echo e($review['user_name']); ?></h3>
                        <p><?php echo nl2br(e($review['review_text'])); ?></p>
                        <div class="review-meta">
                            <?php if (!empty($review['reviewed_at'])): ?>
                                Дата: <?php echo e(date('d.m.Y', strtotime($review['reviewed_at']))); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="content-card">
                    Пока отзывов нет.
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>