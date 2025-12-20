<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Formulaire de connexion';

echo Html::beginTag('div');

    echo Html::beginTag('div', ['class' => 'jumbotron text-center bg-transparent mt-5 mb-5']);

        // Affichage du titre de la page avec une description
        echo Html::tag('h1', Html::encode($this->title));
        echo Html::tag('p', Html::encode("Complétez les champs pour vous connecter."));

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'container mb-5']);

            // Création d'un formulaire en utilisant la classe LoginForm
            $form = ActiveForm::begin([
                'id' => 'login-form',   // <form id='login-form'></form>
                'method' => 'post', // Méthode POST pour plus de sécurité lors de l'envoi des données
                'options' => ['class' => 'form d-flex flex-column flex-wrap gap-3 justify-content-center'], // <form class="..."></form>
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'form-label fw-semibold mb-1'],
                    'inputOptions' => ['class' => 'form-control'],
                    'errorOptions' => ['class' => 'invalid-feedback'],
                ],
            ]);

            // Champ pour le username
            echo $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => "Nom d'utilisateur"])->label("Nom d'utilisateur");

            // Champ pour le mot de passe
            echo $form->field($model, 'password')->passwordInput(['placeholder' => 'Mot de passe'])->label("Mot de passe");

            // Case à cocher pour se souvenir de l'utilisateur
            echo $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            ]);

            // Redirection de l'utilisateur vers le formulaire d'inscription si il n'a pas encore de compte
            echo Html::beginTag('div', ['class' => 'text-center mt-3']);

                echo Html::tag('span', 'Pas encore de compte ? ', ['class' => 'text-muted']);
                echo Html::a('Créez-vous un compte', ['/site/inscription'], ['class' => 'fw-semibold text-primary text-decoration-none',]);

            echo Html::endTag('div');

            // Bouton pour se connecter
            echo Html::beginTag('div', ['class' => 'form-group']);
                echo Html::beginTag('div', ['class' => 'd-flex flex-column gap-2 mt-4']);

                    // Bouton pour se connecter
                    echo Html::submitButton('Connexion', ['class' => 'btn btn-custom', 'name' => 'login-button']);

                echo Html::endTag('div');
            echo Html::endTag('div');

            ActiveForm::end();

        echo Html::endTag('div');
    echo Html::endTag('div');

echo Html::endTag('div');

?>
