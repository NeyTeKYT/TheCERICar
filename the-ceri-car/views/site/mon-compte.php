<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Mon compte';

echo Html::beginTag('div');

    echo Html::beginTag('div', ['class' => 'jumbotron text-center bg-transparent mt-5 mb-5']);

        // Affichage du titre de la page avec une description
        echo Html::tag('h1', Html::encode($this->title));
        echo Html::tag('p', Html::encode("Modifiez vos informations personnelles."));

    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'container mb-5']);

        // Création d'un formulaire en utilisant la classe MonCompteForm
        $form = ActiveForm::begin([
            'id' => 'mon-compte-form',  // <form id='mon-compte-form'></form>
            'method' => 'post', // Méthode POST pour plus de sécurité lors de l'envoi des données
            'options' => ['class' => 'form d-flex flex-column gap-3 justify-content-center'],   // <form class="..."></form>
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'form-label fw-semibold mb-1'],
                'inputOptions' => ['class' => 'form-control'],
                'errorOptions' => ['class' => 'invalid-feedback d-block'],
            ],
        ]);

        // Champ pour le nom
        echo $form->field($model, 'nom')->textInput(['placeholder' => 'Nom'])->label("Nom");

        // Champ pour le prénom
        echo $form->field($model, 'prenom')->textInput(['placeholder' => 'Prénom'])->label("Prénom");

        // Champ pour le username
        echo $form->field($model, 'username')->textInput(['placeholder' => "Nom d'utilisateur"])->label("Nom d'utilisateur");

        // Champ pour le mot de passe
        echo $form->field($model, 'password')->passwordInput(['placeholder' => 'Mot de passe (laissez vide pour ne pas le modifier)'])->label("Mot de passe");

        // Champ pour l'adresse mail
        echo $form->field($model, 'mail')->input('email', ['placeholder' => 'Adresse mail'])->label('Adresse mail');

        // Champ pour le numéro de permis
        echo $form->field($model, 'permis')->textInput(['placeholder' => 'Numéro de permis'])->label("Numéro de permis");

        // Champ pour la photo de profil
        echo $form->field($model, 'photo')->textInput(['placeholder' => 'URL de la photo de profil'])->label("URL vers une photo de profil");

        echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::beginTag('div', ['class' => 'd-flex flex-column gap-2 mt-4']);

                // Bouton pour envoyer le formulaire afin de modifier ses informations
                echo Html::submitButton('Modifier mes informations', ['class' => 'btn btn-custom', 'name' => 'edit-button']);

                // Bouton pour supprimer son compte
                echo Html::button('Supprimer mon compte', ['class' => 'btn btn-danger w-100', 'id' => 'supprimer-compte']);

            echo Html::endTag('div');
        echo Html::endTag('div');

        ActiveForm::end();

        echo Html::endTag('div');
    echo Html::endTag('div');

echo Html::endTag('div');

?>
