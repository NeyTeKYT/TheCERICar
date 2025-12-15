<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $recherche */

use yii\bootstrap5\ActiveForm;
use app\models\Voyage;
use yii\helpers\Html;

$this->title = 'Résumé de votre réservation';
/*$this->params['breadcrumbs'][] = $this->title;*/  // affiche le chemin dans l'arborescence du site

echo Html::beginTag('div', ['class' => 'site-reserver']);

    echo Html::beginTag('div', ['class' => 'jumbotron text-center bg-transparent mt-5 mb-5']);

        // Affichage du titre de la page avec un slogan
        echo Html::tag('h1', Html::encode($this->title));
        echo Html::tag('p', 'Vous êtes sur le point de réserver le voyage suivant.<br>Merci de vérifier les informations avant de confirmer.', ['class' => 'lead']);

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'card voyage-card shadow-sm mb-4']);
        echo Html::beginTag('div', ['class' => 'card-body']);

            Voyage::afficherInformationsReserver($voyage->id, $nb_personnes);

        echo Html::endTag('div'); 
    echo Html::endTag('div'); 
    
    // Formulaire pour confirmer la réservation
    echo Html::beginTag('div', ['class' => 'd-flex justify-content-between align-items-center mt-4']);

        echo Html::a('← Modifier ma recherche', ['site/index'], ['class' => 'btn btn-outline-secondary']);

        echo Html::beginForm(['site/confirmer-reservation'], 'post');

            echo Html::hiddenInput('voyage_id', $voyage->id);

            echo Html::hiddenInput('nb', $nb_personnes);

            echo Html::submitButton('Confirmer la réservation',['class' => 'btn btn-success btn-lg']);
        
        echo Html::endForm();

    echo Html::endTag('div'); 


echo Html::endTag('div');

?>