<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\RegistrationForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = "Formulaire d'inscription";
//$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'site-inscription']);

    echo Html::beginTag('div', ['class' => 'jumbotron text-center bg-transparent mt-5 mb-5']);

        // Affichage du titre de la page avec une description
        echo Html::tag('h1', Html::encode($this->title));
        echo Html::tag('p', Html::encode("Veuillez compléter les champs pour vous inscrire :"));

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'container mb-5']);

            // Création d'un formulaire en utilisant la classe RegistrationForm
            $form = ActiveForm::begin([
                'id' => 'registration-form',   // <form id='registration-form'></form>
                'method' => 'post', // Méthode POST pour plus de sécurité lors de l'envoi des données
                'options' => ['class' => 'search-form d-flex flex-column flex-wrap gap-3 justify-content-center'], // <form class="..."></form>
                'fieldConfig' => [
                    'template' => "{input}\n{error}",
                    'inputOptions' => ['class' => 'col-lg-3 form-control'],
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                ],
            ]);

            // Champ pour le nom
            echo $form->field($model, 'nom')->textInput(['autofocus' => true, 'placeholder' => "Nom"]);

            // Champ pour le prénom
            echo $form->field($model, 'prenom')->textInput(['placeholder' => "Prénom"]);

            // Champ pour le username
            echo $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => "Nom d'utilisateur"]);

            // Champ pour le mot de passe
            echo $form->field($model, 'password')->passwordInput(['placeholder' => 'Mot de passe']);

            // Champ pour l'adresse mail
            echo $form->field($model, 'mail')->textInput(['placeholder' => "Adresse mail"]);

            // Champ pour le numéro de permis
            echo $form->field($model, 'permis')->textInput(['placeholder' => "Numéro de permis"]);

            // Champ pour la photo de profil
            echo $form->field($model, 'photo')->textInput(['placeholder' => "URL vers une photo"]);

            // Case à cocher pour se souvenir de l'utilisateur
            echo $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            ]);

            // Redirection de l'utilisateur vers le formulaire de connexion
            echo Html::beginTag('div', ['class' => 'text-center mt-3']);

                echo Html::tag('span', 'Vous avez déjà un compte ? ', ['class' => 'text-muted']);
                echo Html::a('Connectez-vous', ['/site/login'], ['class' => 'fw-semibold text-primary text-decoration-none',]);

            echo Html::endTag('div');


            echo Html::beginTag('div', ['class' => 'form-group']);
                echo Html::beginTag('div', ['class' => 'd-flex flex-column gap-2 mt-4']);

                    // Bouton pour se créer un compte
                    echo Html::submitButton('Inscription', ['class' => 'btn btn-custom', 'name' => 'registration-button']);

                echo Html::endTag('div');
            echo Html::endTag('div');

            ActiveForm::end();

        echo Html::endTag('div');
    echo Html::endTag('div');

echo Html::endTag('div');

?>
