// Affiche la notification dans le bandeau
function showNotification(message, success = true) {

    // Mise à jour du contenu du bandeau de notification
    $('#notification')
        .stop(true, true)
        .text(message)
        .removeClass('alert-success alert-danger')
        .addClass(success ? 'alert-success' : 'alert-danger')
        .fadeIn();  // Permet au bandeau de notification d'arriver de manière smooth (proposé par JQuery)

    $('html, body').animate({ scrollTop: 0 }, 'fast');  // Permet de revenir en haut de la page pour que l'utilisateur puisse voir le bandeau de notification une fois chargé

}

// Soumission du formulaire de recherche d'un voyage
$(document).on('submit', '#recherche-form', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: '/site/recherche', // URL vers l'action dans le controller
        type: 'GET',
        data: $(this).serialize(),  // Adapte le format JSON en string

        /* J'ai choisi JSON au lieu d'un renderPartial avec HTML car JSON est un format de données structurées en paires clé/valeur, 
        utilisé pour l'échange d'informations entre navigateurs et serveurs */
        dataType: 'json',   

        success: function(data) {

            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);

            // Mise à jour des résultats obtenus
            $('#resultats').html(data.html);

        },

        error: function() {
            // Mise à jour du contenu du bandeau
            showNotification("Une erreur est survenue.", false);
        }

    });

});

// Connexion de l'utilisateur
$(document).on('submit', '#login-form', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: '/site/login', // URL vers l'action dans le controller
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',   // Adapte le format JSON en string

        success: function(data) {

            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);

            // Redirige l'utilisateur en cas de succès sur la page d'accueil au bout de 5 secondes (le temps pour lui de lire la notification)
            if(data.success) setTimeout(() => {window.location.href = '/site/index';}, 5000);

        },

        error: function() {
            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);
        }
    });
});

// Inscription de l'utilisateur
$(document).on('submit', '#registration-form', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: '/site/inscription',   // URL vers l'action dans le controller
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',   // Adapte le format JSON en string

        success: function(data) {

            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);

            // Redirige l'utilisateur en cas de succès sur la page d'accueil au bout de 5 secondes (le temps pour lui de lire la notification)
            if(data.success) setTimeout(() => {window.location.href = '/site/index';}, 5000);

        },

        error: function() {
            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);
        }
    });
});

// Déconnexion de l'utilisateur
$(document).on('submit', '#logout-form', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: '/site/logout',    // URL vers l'action dans le controller
        type: 'POST',
        data: {
            _csrf: yii.getCsrfToken()
        },
        dataType: 'json',   // Adapte le format JSON en string

        success: function(data) {

            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);

            // Redirige l'utilisateur en cas de succès sur la page d'accueil au bout de 5 secondes (le temps pour lui de lire la notification)
            if(data.success) setTimeout(() => {window.location.href = '/site/index';}, 5000);

        },

        error: function() {
            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);
        }

    });

});

// Ajouter une réservation à un voyage
$(document).on('click', '.reserver-voyage', function () {

    $.ajax({
        url: '/site/reserver',
        type: 'POST',
        data: {
            id_voyage: $(this).data('id_voyage'),
            nb_personnes: $(this).data('nb_personnes'),
            _csrf: yii.getCsrfToken()
        },
        dataType: 'json',

        success: function (data) {

            showNotification(data.notification, data.success);

            if(data.success) setTimeout(() => {window.location.href = '/site/mes-reservations'}, 3000);

        },

        error: function () {
            showNotification('Erreur lors de la réservation.', false);
        }
    });
});

// Soumission du formulaire pour modifier une réservation
$(document).on('click', '#modifier-reservation-form', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: $(this).attr('action'),  // URL vers l'action dans le controller
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function(data) {

            showNotification(data.notification, data.success);

            if(data.success) setTimeout(() => {window.location.href = '/site/mes-reservations';}, 5000);
            
        },

        error: function() {
            showNotification('Erreur lors de la modification.', false);
        }
    });
});

// Clique sur le bouton pour supprimer une réservation
$(document).on('click', '.supprimer-reservation', function (e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: '/site/supprimer-reservation',
        type: 'POST',
        data: {
            id: $(this).data('id'),
            _csrf: yii.getCsrfToken()
        },
        dataType: 'json',

        success: function(data) {

            showNotification(data.notification, data.success);

            if(data.success) setTimeout(() => {location.reload();}, 1000);

        },

        error: function() {
            showNotification('Erreur lors de la suppression.', false);
        }
    });
});

// Ajout / Modification d'un voyage proposé par un conducteur
$(document).on('click', '#proposer-voyage', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: $(this).attr('action'),   // URL vers l'action dans le controller en fonction de l'action du formulaire (si $voyage_id existe ou pas)
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',   // Adapte le format JSON en string

        success: function(data) {

            // Mise à jour du contenu du bandeau de notification
            showNotification(data.notification, data.success);

            if(data.success) {
                $('#proposer-voyage')[0].reset(); // Réinitialise les champs du formulaire
                $('html, body').animate({ scrollTop: 0 }, 'fast'); // Remonte tout en haut pour pouvoir voir le bandeau
                setTimeout(() => {window.location.href = '/site/mes-voyages';}, 3000);  // Redirection vers la liste des voyages proposés par le conducteur au bout de 3 secondes
            }

        },

        error: function(data) {
            // Mise à jour du contenu du bandeau de notification
            showNotification(data.notification, data.success);
        }
    });
});

// Suppression d'un voyage proposé par un conducteur
// On n'utilise pas de ID car il y a plusieurs boutons donc une chaque bouton à cette classe
$(document).on('click', '.supprimer-voyage', function () {

    $.ajax({
        url: '/site/supprimer-voyage',
        type: 'POST',
        data: {
            id: $(this).data('id'),
            _csrf: yii.getCsrfToken()
        },
        dataType: 'json',

        success: function (data) {
            // Mise à jour du contenu du bandeau de notification
            showNotification(data.notification, data.success);
            if(data.success) setTimeout(() => location.reload(), 1000); // Actualise la page
        },

        error: function (data) {
            // Mise à jour du contenu du bandeau de notification
            showNotification(data.notification, data.success);
        }

    });

});

// Modification du compte de l'utilisateur
$(document).on('submit', '#mon-compte-form', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: '/site/mon-compte',    // URL vers l'action dans le controller
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',   // Adapte le format JSON en string

        success: function(data) {

            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);
            
            // Redirige l'utilisateur en cas de succès sur la page d'accueil au bout de 5 secondes (le temps pour lui de lire la notification)
            if(data.success) setTimeout(() => {window.location.href = '/site/index';}, 5000);

        },

        error: function(data) {
            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);
        }
    });
});

// Suppression du compte de l'utilisateur
$(document).on('click', '#supprimer-compte', function() {

    $.ajax({
        url: '/site/supprimer-compte',  // URL vers l'action dans le controller
        type: 'POST',
        data: {
            _csrf: yii.getCsrfToken()
        },
        dataType: 'json',   // Adapte le format JSON en string

        success: function (data) {

            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);
            
            // Redirige l'utilisateur en cas de succès sur la page d'accueil au bout de 5 secondes (le temps pour lui de lire la notification)
            if(data.success) setTimeout(() => {window.location.href = '/site/index';}, 5000);
            
        },

        error: function () {
            // Mise à jour du contenu du bandeau
            showNotification(data.notification, data.success);
        }
    });

});








