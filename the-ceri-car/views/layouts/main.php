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
use app\models\Reservation;

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
            // Titre de l'application web
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar navbar-expand-md navbar-light navbar-custom fixed-top']
        ]);

        // Affichage de l'onglet "Rechercher un voyage" disponible pour n'importe quel utilisateur = page d'accueil
        $items = [['label' => 'Rechercher un voyage', 'url' => ['/site/index']],];

        // Affichage de l'onglet "Connexion" si il n'est pas connecté
        // Ou si il est connecté, affichage de l'onglet "Déconnexion (pseudo)" pour se déconnecter
        if (Yii::$app->user->isGuest) $items[] = ['label' => 'Authentification', 'url' => ['/site/login']];

        // Sinon, alors l'utilisateur est connecté et peut se déconnecter via l'onglet "Déconnexion (son pseudo)"
        else {

            // Page pour voir et modifier les informations de l'utilisateur connecté
            $items[] = ['label' => 'Mon compte', 'url' => ['/site/mon-compte']];

            $reservations = Reservation::findReservationsByUserId(Yii::$app->user->id); // Récupération des réservations de l'utilisateur
            // Affichage la page "Mes réservations" si il en a effectué au moins une
            if(!empty($reservations)) $items[] = ['label' => 'Mes réservations', 'url' => ['/site/mes-reservations']];

            // Si l'utilisateur a renseigné son permis 
            if(!empty(Yii::$app->user->identity->permis)) {
                
                $items[] = ['label' => 'Proposer un voyage', 'url' => ['/site/proposer']];  // Alors il peut proposer un voyage

                $voyages = Voyage::findVoyagesByUserId(Yii::$app->user->id);    // Récupérations des voyages proposés par l'utilisateur connecté
                if(!empty($voyages)) $items[] = ['label' => 'Mes voyages', 'url' => ['/site/mes-voyages']];

            }

            // Bouton pour se déconnecter de l'application web
            $items[] =
                '<li class="nav-item">'
                . Html::beginForm(
                    ['/site/logout'],
                    'post',
                    ['id' => 'logout-form']
                )
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
            echo Html::beginTag('div', ['id' => 'notification', 'class' => 'text-center alert', 'style' => 'display:none;']);
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
