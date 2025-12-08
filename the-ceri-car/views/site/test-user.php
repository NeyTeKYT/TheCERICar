<?php

    use yii\helpers\Html;
    use app\models\User;
    use app\models\Trajet;
    use app\models\Voiture;
    use app\models\Voyage;
    use app\models\Reservation;

?>

<div>

	<h1><?= Html::encode($this->title = 'TheCeriCar - Test User') ?></h1>

    <?php 
    
        //var_dump($user);

        if($user != NULL) {
            
            User::afficherInformations($user);

            if($voyages != NULL) {
                
                // Compte le nombre de voyages proposés par le conducteur
                $nb_voyages = count($voyages);
                echo "<h2>$user->nom $user->prenom propose $nb_voyages voyages : </h2>";

                foreach($voyages as $voyage) Voyage::afficherInformations($voyage);

            }
            
            else {
                // Vérifie si l'utilisateur est conducteur ou pas, ou s'il n'a juste pas publié de voyage
                if($user->permis == NULL) echo "<h2>L'utilisateur n'est pas conducteur !</h2>";
                else echo "<h2>L'utilisateur est conducteur mais n'a pas de voyages publiés en ligne !</h2>";   
            }

            if($reservations != NULL) {

                // Compte le nombre de réservations effectuées par l'utilisateur
                $nb_reservations = count($reservations);
                echo "<h2>$user->nom $user->prenom a réservé $nb_voyages voyages : </h2>";

                foreach($reservations as $reservation) Reservation::afficherInformations($reservation);

            }
            else echo "<h2>L'utilisateur n'a effectué aucune réservation !</h2>";
        }
        else echo "<h2>L'utilisateur n'existe pas !</h2>";

    ?>

</div>