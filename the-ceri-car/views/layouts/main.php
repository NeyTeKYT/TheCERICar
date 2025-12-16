<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use app\models\Voyage;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

// Ajout de mon fichier JS contenant la/les requête(s) Ajax
$this->registerJsFile("@web/js/script.js", [
    'depends' => [
        \yii\web\JqueryAsset::className()
    ]
]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <!-- Police Lobster pour le titre de l'application "TheCeriCar" -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">

</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<!-- Barre de navigation -->
<header id="header">
    <?php

        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md navbar-light navbar-custom fixed-top']
        ]);

        // La recherche d'un voyage est autorisée pour tous les clients 
        $items = [
            ['label' => 'Rechercher un voyage', 'url' => ['/site/index']],
        ];

        // Si l'utilisateur est connecté et a un permis 
        if (!Yii::$app->user->isGuest && !empty(Yii::$app->user->identity->permis)) {

            // Alors il est en mesure de proposer un voyage
            $items[] = ['label' => 'Proposer un voyage', 'url' => ['/site/proposer']];

            $voyages = Voyage::findVoyagesByUserId(Yii::$app->user->id);
            if(!empty($voyages)) $items[] = ['label' => 'Mes voyages', 'url' => ['/site/mes-voyages']];

        }

        // Connexion / Déconnexion
        if (Yii::$app->user->isGuest) $items[] = ['label' => 'Authentification', 'url' => ['/site/login']];
        else {
            $items[] =
                '<li class="nav-item">'
                . Html::beginForm(['/site/logout'])
                . Html::submitButton(
                    'Déconnexion (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'nav-link btn btn-link logout']
                )
                . Html::endForm()
                . '</li>';
        }

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => $items,
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
        <?php 

            // Bandeau de notification
            echo Html::beginTag('div', ['id' => 'notification', 'class' => 'text-center alert']);
            echo Html::endTag('div');

        ?>
        <?= $content ?>
    </div>

</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center">&copy; TheCeriCar <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><em>Florent CAGNARD</em></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
