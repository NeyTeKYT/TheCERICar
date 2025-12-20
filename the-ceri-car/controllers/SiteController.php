<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\db\Query;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use app\models\Voyage;
use app\models\Reservation;
use app\models\RechercheForm;
use app\models\Trajet;
use app\models\RegistrationForm;
use app\models\ProposerForm;
use app\models\MonCompteForm;
use app\models\Internaute;

class SiteController extends Controller {
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Affiche la page d'accueil
     * 
     */
    public function actionIndex() {

        // Création d'une instance pour modéliser le formulaire de recherche
        $recherche = new RechercheForm();

        // Affichage de la page "pour la première fois" quand l'utilisateur a cliqué sur l'onglet
        return $this->render('index', ['recherche' => $recherche, 'resultats' => null]);

    }

    /**
     * Recherche d'un (ou plusieurs) voyage(s) en fonction d'une recherche effectuée par le client.
     * 
     */
    public function actionRecherche() {

        // Création d'une instance pour modéliser le formulaire de recherche
        $recherche = new RechercheForm();

        // Initialise les attributs de l'instance RechercheForm à partir des valeurs transmises via le formulaire
        $recherche->load(Yii::$app->request->get(), 'RechercheForm');

        // Par défaut, aucun résultat n'a été trouvé
        $resultats = null;

        // Si la recherche est valide = pas d'erreurs détectée (voir la méthode rules())
        if($recherche->validate()) {
            $resultats = RechercheForm::lancerRecherche(
                $recherche->nb_personnes,
                false,  // pour le moment on ne traite pas encore les correspondances
                $recherche->ville_depart,
                $recherche->ville_arrivee
            );

            // Vérifie que le trajet entré par l'utilisateur existe dans la BDD
            $trajet_recherche = Trajet::getTrajet($recherche->ville_depart, $recherche->ville_arrivee);
            if(!$trajet_recherche) $notification = "Le trajet renseigné est indisponible !";

            else if($resultats) {

                // Récupération du nombre de voyages disponibles = encore réservables
                $nb_voyages_dispo = 0;
                foreach($resultats as $voyage) if(Voyage::verifierDisponibilite($voyage->id, $recherche->nb_personnes)) $nb_voyages_dispo++;

                // Messages différents dans la barre de notification selon si un ou plusieurs voyages ont été trouvés
                if($nb_voyages_dispo > 1) $notification = "Plusieurs voyages ont été trouvés correspondants à votre recherche !";
                else if($nb_voyages_dispo == 1) $notification = "Un voyage a été trouvé correspondant à votre recherche !"; 
                else $notification = "Tous les voyages disponibles ne permettent pas d'accueillir $recherche->nb_personnes passagers !";

            }
            
            else $notification = "Aucun voyage correspondant à votre recherche !";

            // Retourne les données via JSON
            return $this->asJson(['notification' => $notification,
                // renderAjax() ne retourne que la vue avec les modifications effectuées
                'html' => $this->renderAjax('_resultats', [     // les vues partielles sont nommées _[nom de la view].php !
                    'resultats' => $resultats,
                    'recherche' => $recherche
                ])
            ]);

        }

        return $this->asJson(['notification' => "Votre recherche est invalide ! Veuillez réessayer ultérieurement.", 'html' => ""]);

    }

    /**
     * Réservation d'un voyage après avoir effectué une recherche.
     * 
     */
    public function actionReserver() {

        Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

        // Empêche l'utilisateur de réserver un voyage si il n'est pas connecté
        if(Yii::$app->user->isGuest) return ['success' => false, 'notification' => 'Vous devez être connecté pour réserver un voyage.'];

        // Récupération des champs cachés envoyés lors de la requête POST au clique sur le bouton
        $id_voyage = Yii::$app->request->post('id_voyage');
        $nb_personnes = Yii::$app->request->post('nb_personnes');

        // Récupération de l'instance voyage
        $voyage = Voyage::getVoyageById($id_voyage);

        // Vérifie que le voyage existe bien (peut avoir été supprimé entre temps)
        if(!$voyage) return ['success' => false, 'notification' => 'Voyage indisponible !'];

        // Empêche le conducteur de réserver son propre voyage
        if($voyage->conducteur == Yii::$app->user->id) return ['success' => false, 'notification' => 'Vous êtes le conducteur de ce voyage ! Vous ne pouvez pas effectuer de réservation à bord de votre propre voyage !'];

        // Vérifie la disponibilité du voyage en fonction du nombre de personnes
        if(!Voyage::verifierDisponibilite($voyage->id, $nb_personnes)) return $this->asJson(['success' => false, 'notification' => "Ce voyage n'est plus disponible !"]);

        // Création d'une instance de la classe Reservation
        $reservation = new Reservation();
        // Ajout des valeurs aux attributs de l'instance
        $reservation->voyageur = Yii::$app->user->id;
        $reservation->voyage = $voyage->id;
        $reservation->nbplaceresa = $nb_personnes;

        // Si la réservation n'a pas pu être insérée dans la base de données
        if(!$reservation->save()) return ['success' => false, 'notification' => 'Erreur lors de la réservation.'];
        // Réservation bien insérée dans la base de données
        else return ['success' => true, 'notification' => 'Réservation effectuée ! Vous allez être automatiquement redirigé vers la liste de vos réservations.'];

    }

    /**
     * Affiche tous les voyages proposés par un conducteur.
     * 
     */
    public function actionMesVoyages() {

        // Redirige l'utilisateur vers la page de connexion s'il tente d'accéder à cette page alors qu'il n'est pas connecté
        if(Yii::$app->user->isGuest) return $this->redirect(['site/login']);

        // Récupération des voyages proposés par l'utilisateur
        $voyages = Voyage::findVoyagesByUserId(Yii::$app->user->id);

        // Retourne la page avec le tableau de voyages récupérés
        return $this->render('mes-voyages', ['voyages' => $voyages]);

    }

    /**
     * Ajout d'un voyage proposé par un conducteur.
     * 
     */
    public function actionProposer() {

        // Création du model pour représenter le formulaire avec les champs entrés par l'utilisateur
        $model = new ProposerForm();

        // Gestion de la requête Ajax POST
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

            // Empêche la création d'un même voyage
            if(Voyage::find()->where(['conducteur' => Yii::$app->user->id, 'heuredepart' => $model->heuredepart, 'trajet' => $model->trajet,])->exists()) return false;

            if($model->proposerVoyage(Yii::$app->user->id)) {
                return [
                    'success' => true,
                    'notification' => "Voyage publié ! Vous allez être automatiquement redirigé vers la liste de vos voyages.",
                ];
            } 
            
            else {
                return [
                    'success' => false,
                    'notification' => 'Une erreur est survenue lors de la publication de votre voyage. Veuillez réessayer ultérieurement.',
                ];
            }
        }

        // Pour un affichage classique si jamais la page est chargée directement
        return $this->render('proposer', ['model' => $model]);

    }

    /**
     * Modification d'un voyage proposé par un conducteur.
     * 
     */
    public function actionModifierVoyage($id) {

        // Redirige l'utilisateur vers la page de connexion s'il tente d'accéder à cette page alors qu'il n'est pas connecté
        if(Yii::$app->user->isGuest) return $this->redirect(['site/login']);

        $voyage = Voyage::getVoyageById($id);

        $model = new ProposerForm();
        $model->loadFromVoyage($voyage);
        $model->voyage_id = $voyage->id;

        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

            if($model->updateVoyage($voyage)) {

                return [
                    'success' => true,
                    'notification' => 'Voyage modifié avec succès ! Vous allez être automatiquement redirigé vers la liste de vos voyages.',
                ];

            }

            return [
                'success' => false,
                'notification' => 'Une erreur est survenue lors de la modification du voyage. Veuillez réessayer ultérieurement.',
            ];
        }

        return $this->render('proposer', ['model' => $model]);

    }

    /**
     * Suppression d'un voyage proposé par un conducteur.
     * 
     */
    public function actionSupprimerVoyage() {

        Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

        // Récupère l'ID fournit via la soumission du bouton
        $id = Yii::$app->request->post('id');
        // Récupération du voyage associé à l'ID récupéré
        $voyage = Voyage::getVoyageById($id);

        if(!$voyage) return ['success' => false, 'notification' => 'Voyage indisponible !'];

        // Vérifie que l'utilisateur connecté est bien le conducteur du voyage
        if($voyage->conducteur != Yii::$app->user->id) return ['success' => false, 'notification' => 'Seul le conducteur peut supprimer ce voyage !'];

        // Transaction pour effectuer plusieurs requêtes SQL liées en même temps : 
        // Suppression de toutes les réservations liées au voyage
        // Suppression du voyage
        $transaction = Yii::$app->db->beginTransaction();

        try {

            // Suppression de toutes les réservations de ce voyage
            Reservation::deleteAll(['voyage' => $voyage->id]);

            // Suppression du voyage
            $voyage->delete();

            $transaction->commit();

            return [
                'success' => true,
                'notification' => 'Le voyage et les réservations effectuées sur celui-ci ont été supprimées avec succès !',
            ];

        } 
        
        catch (\Throwable $e) {

            $transaction->rollBack();   // Retour en arrière : on remet le voyage et les réservations dans la base de données

            return [
                'success' => false,
                'notification' => 'Une erreur est survenue lors de la suppression du voyage. Veuillez réessayer ultérieurement.',
            ];
        }
    }

    /**
     * Affiche les réservations effectuées par l'utilisateur connecté.
     */
    public function actionMesReservations() {

        // Redirige l'utilisateur vers la page de connexion s'il n'est pas connecté
        if(Yii::$app->user->isGuest) return $this->redirect(['site/login']);

        // Récupère toutes les réservations de l'utilisateur
        $reservations = Reservation::findReservationsByUserId(Yii::$app->user->id);

        // Affiche la vue 'mes-reservations.php' en lui passant les réservations
        return $this->render('mes-reservations', ['reservations' => $reservations]);

    }

    /**
     * Modifie une réservation existante
     * 
     */
    public function actionModifierReservation($id) {

        // Redirection si l'utilisateur n'est pas connecté
        if(Yii::$app->user->isGuest) return $this->redirect(['site/login']);

        // Récupération de la réservation
        $reservation = Reservation::getReservationById($id);
        if(!$reservation) return $this->redirect(['site/mes-reservations']);

        // Vérifie que la réservation appartient bien à l'utilisateur connecté
        if($reservation->voyageur != Yii::$app->user->id) {
            Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"
            return ['success' => false, 'notification' => "Vous ne pouvez pas modifier cette réservation ! Vous n'êtes pas le propriétaire de cette réservation !"];
        }

        if(Yii::$app->request->isAjax && $reservation->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

            // Vérifie que le nombre de places est positif
            if($reservation->nbplaceresa < 1) return ['success' => false, 'notification' => 'Le nombre de places doit être au moins 1.'];

            // Récupération du voyage
            $voyage = Voyage::getVoyageById($reservation->voyage);

            // Récupération de toutes les réservations du voyage
            $reservations = Reservation::getReservationsByVoyageId($voyage->id);

            // Calcul des places déjà réservées (SAUF la réservation en cours)
            $nb_places_reservees = 0;
            foreach($reservations as $r) if($r->id != $reservation->id) $nb_places_reservees += $r->nbplaceresa;

            // Calcul des places disponibles
            $nb_places_disponibles = $voyage->nbplacedispo - $nb_places_reservees;

            // Vérification que l'utilisateur n'a pas choisi un nombre supérieur au nombre de places dispos
            if($reservation->nbplaceresa > $nb_places_disponibles) return ['success' => false, 'notification' => 'Le nombre de places demandé dépasse la disponibilité du voyage.'];

            $ancienneValeur = $reservation->getOldAttribute('nbplaceresa');

            // Vérifie si la réservation a bien été insérée dans la BDD
            if($reservation->save()) {
                if($reservation->nbplaceresa == $ancienneValeur) return ['success' => false, 'notification' => "Le nombre de places réservées n'a pas changé ! Vous allez être automatiquement redirigé vers la liste de vos réservations."];
                else return ['success' => true, 'notification' => 'Réservation modifiée avec succès ! Vous allez être automatiquement redirigé vers la liste de vos réservations.'];
            }
            else return ['success' => false, 'notification' => 'Erreur lors de la modification de la réservation.'];

        }

        // Affichage de la page de modification
        return $this->render('modifier-reservation', ['reservation' => $reservation]);
    }

    /**
     * Supprime une réservation
     * 
     */
    public function actionSupprimerReservation() {

        Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

        // Redirection si l'utilisateur n'est pas connecté
        if(Yii::$app->user->isGuest) return $this->redirect(['site/login']);

        // Récupération de l'ID de la réservation à supprimer envoyé via la requête POST
        $id = Yii::$app->request->post('id');

        // Récupération de la réservation
        $reservation = Reservation::getReservationById($id);

        // Cas où la réservation n'existe pas
        if(!$reservation) return ['success' => false, 'notification' => 'Réservation introuvable !'];

        // Vérifie que la réservation appartient à l'utilisateur connecté
        if($reservation->voyageur != Yii::$app->user->id) return ['success' => false, 'notification' => 'Vous ne pouvez pas supprimer cette réservation.'];
            
        // Si la réservation a bien été supprimée
        if($reservation->delete()) return ['success' => true, 'notification' => 'Réservation supprimée avec succès !'];

        return ['success' => false, 'notification' => 'Erreur lors de la suppression de la réservation.'];
    }

    /**
     * Connexion de l'utilisateur une fois le formulaire soumis.
     *
     */
    public function actionLogin() {

        if(!Yii::$app->user->isGuest) return $this->goHome();   // N'autorise pas l'utilisateur à accéder à la page si il est connecté

        // Création d'un objet pour modéliser le formulaire
        $model = new LoginForm();

        // Si les données de la requête POST ont bien été chargées dans l'instance LoginForm
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

            // Si l'utilisateur a donné des identifiants qui correspondent à un enregistrement dans la BDD
            if($model->login()) {
                return ['success' => true, 'notification' => "Connexion réussie ! Vous allez être automatiquement redirigé vers la page d'accueil.",];
                return $this->goBack();
            }

            // Problème lors de la vérification des identifiants de l'utilisateur pour se connecter
            $errors = $model->getFirstErrors();
            return ['success' => false, 'notification' => implode("\n", $errors)];

        }

        return $this->render('login', ['model' => $model]);

    }

    /**
     * Inscription de l'utilisateur une fois le formulaire soumis.
     * 
     */
    public function actionInscription() {

        if(!Yii::$app->user->isGuest) return $this->goHome();   // N'autorise pas l'utilisateur à accéder à la page si il est connecté

        // Création d'un objet pour modéliser le formulaire
        $model = new RegistrationForm();

        // Si les données de la requête POST ont bien été chargées dans l'instance RegisterForm
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

            // Si l'utilisateur a bien été ajouté dans la BDD
            if($model->register()) return ['success' => true, 'notification' => "Inscription réussie ! Vous allez être automatiquement redirigé vers la page d'accueil."];

            // Problème lors de l'insertion de l'internaute dans la BDD donc affichage de toutes les erreurs trouvées 
            $errors = $model->getFirstErrors();
            return ['success' => false, 'notification' => implode("\n", $errors)];

        }

        return $this->render('inscription', ['model' => $model]);
    }

    /**
     * Déconnecte de l'utilisateur sur l'application web.
     *
     */
    public function actionLogout() {

        Yii::$app->user->logout();  // Déconnecte l'utilisateur de l'application web

        if(Yii::$app->request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

            // Affichage d'un message dans le bandeau de notification pour annoncer à l'utilisateur qu'il a bien été déconnecté.
            return ['success' => true, 'notification' => "Vous avez été déconnecté avec succès ! Vous allez être automatiquement redirigé vers la page d'accueil.",];

        }

        return $this->goHome();

    }

    /**
     * Accède à la page des paramètres du compte de l'utilisateur connecté pour modifier ses informations.
     * 
     */
    public function actionMonCompte() {

        // Redirige l'utilisateur vers le formulaire de connexion si il tente d'accéder à la page alors qu'il n'est pas connecté
        if(Yii::$app->user->isGuest) return $this->redirect(['site/login']);

        // Récupère l'instance de l'utilisateur grâce à son ID
        $user = User::findIdentity(Yii::$app->user->id);

        $model = new MonCompteForm();   // Création de l'instance MonCompteForm qui modélise le formulaire
        $model->loadFromUser($user);    // Chargement des données de l'utilisateur connecté dans chaque champ du formulaire
        $model->id = $user->id; // Ajoute la valeur de l'ID de l'utilisateur connecté

        // Si les données de la requête POST ont bien été chargées dans l'instance RegisterForm
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

            Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

            // Si les informations de l'internaute ont bien été modifiées dans la BDD
            if($model->update($user)) return ['success' => true, 'notification' => "Informations mises à jour avec succès ! Vous allez être automatiquement redirigé vers la page d'accueil."];
            
            // Problème lors de l'insertion de l'internaute dans la BDD donc affichage de toutes les erreurs trouvées 
            $errors = $model->getFirstErrors();
            return ['success' => false, 'notification' => implode("\n", $errors)];

        }

        return $this->render('mon-compte', ['model' => $model]);

    }

    /**
     * Supprime le compte de l'utilisateur.
     * 
     */
    public function actionSupprimerCompte() {

        // Redirige l'utilisateur vers le formulaire de connexion si il tente d'accéder à la page alors qu'il n'est pas connecté
        if(Yii::$app->user->isGuest) return $this->redirect(['site/login']);

        Yii::$app->response->format = Response::FORMAT_JSON;    // Évite de faire "return $this->asJson()"

        $userId = Yii::$app->user->id;  // Récupération de l'ID de l'utilisateur connecté

        // Transaction pour effectuer plusieurs requêtes SQL sur la base de données
        $transaction = Yii::$app->db->beginTransaction();

        try {

            // Suppression de toutes les réservations effectuées par l'utilisateur
            Reservation::deleteAll(['voyageur' => $userId]);

            // Récupération de tous les ID des voyages proposés par le conducteur
            $voyageIds = Voyage::find()->select('id')->where(['conducteur' => $userId])->column();
            if(!empty($voyageIds)) Reservation::deleteAll(['voyage' => $voyageIds]);

            // Suppression de tous les voyages proposés par l'utilisateur
            Voyage::deleteAll(['conducteur' => $userId]);

            // Suppression de l'internaute
            Internaute::deleteAll(['id' => $userId]);

            // Mise à jour de la base de données
            $transaction->commit();

            // Déconnexion de l'utilisateur
            Yii::$app->user->logout();
            
            return ['success' => true, 'notification' => "Votre compte a été supprimé avec succès ! Vous allez être automatiquement redirigé vers la page d'accueil."];

        } 
        
        // Erreur lors de la suppression de l'utilisateur
        catch (\Throwable $e) {

            $transaction->rollBack();   // Revenir en arrière pour effacer les modifications effectuées

            return ['success' => false, 'notification' => $e->getMessage()];
        }
    }

    /**
     * Partie 2 de l'AMS : Affiche les informations d'un utilisateur et toutes informations liées (réservations, voyages).
     * 
     */
    public function actionTestUser() {

        // Mise en place de l'argument pseudo dans l'URL
        $request = Yii::$app->request;
        $pseudo = $request->get('pseudo');

        // Récupération de l'utilisateur dans la table à partir de la valeur de l'argument
        $user = User::findByPseudo($pseudo);

        if($user) {

            // Récupération d'une liste de voyages proposés par le conducteur si il a enregistré son permis
            if($user->permis != NULL) $voyages = Voyage::findVoyagesByUserId($user->id); 
            else $voyages = NULL;

            // Récupération d'une liste de réservations enregistrées par l'utilisateur 
            $reservations = Reservation::findReservationsByUserId($user->id);

            // Fournit à la view les instances des classes crées pour afficher leurs données
            return $this->render('test-user', ['user' => $user, 'voyages' => $voyages, 'reservations' => $reservations]);
        }
        else return $this->render('test-user', ['user' => $user]);

    }

}
