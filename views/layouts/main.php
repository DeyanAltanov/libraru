<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\models\Readers;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
if (isset($_SESSION['order'])){
    $cartAmount = count($_SESSION['order']);
}
else {
    $cartAmount = 0;
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => 'Library',
        'brandUrl' => '/site/catalogue',
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'encodeLabels' => false,
        'items' => [
            !Yii::$app->user->isGuest
            ? ['label' => 'Profile Info', 'url' => ['/readers/view', 'ID' => Yii::$app->user->identity->ID]]
            :'',
            ['label' => 'Books',
                'items' => [
                    ['label' => 'Add book', 'url' => '/books/create'],
                    ['label' => 'Overdue books', 'url' => '/books/overdue-books'],
                    ['label' => 'Add genre', 'url' => '/genres/create'],
                    ['label' => 'All genres', 'url' => '/genres/all-genres'],
                ], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->Type != 'Reader',
            ],
            ['label' => 'Users',
                'items' => [
                    ['label' => 'Create user', 'url' => '/readers/create'],
                    ['label' => 'All users', 'url' => '/readers/all-users'],
                ], 'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->Type != 'Reader',
            ],
            ['label' => 'Catalogue', 'url' => ['/site/catalogue']],
            ['label' => 'Contact', 'url' => ['/site/contact']],
            Yii::$app->user->isGuest
                ? ['label' => 'Login', 'url' => ['/site/login']]
                : '<li class="nav-item">'
                    . Html::beginForm(['/site/logout'])
                    . Html::submitButton(
                        'Logout (' . Yii::$app->user->identity->FirstName . ')',
                        ['class' => 'nav-link btn btn-link logout', 'data' => [
                            'confirm' => 'Are you sure you want to logout?'
                        ],]
                    )
                    . Html::endForm()
                    . '</li>',
            Yii::$app->user->isGuest
                ? ['label' => 'Signup', 'url' => ['/site/signup']]
                :'',
            ['label' => '<img src="/images/Books.jpg" id="books">'. (empty($cartAmount)?'':'<p id="cart-amount" class="amnt badge badge-secondary">' . $cartAmount.'</p>'), 'options' => ['class' =>'cart books-nav'], 'url' => ['/reserved-books/current-order'], 'visible' => !Yii::$app->user->isGuest],
        ]
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; All Rights Reserved. <?= date('Y') ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>