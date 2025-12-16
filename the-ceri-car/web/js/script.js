// Soumission du formulaire de recherche d'un voyage
$('#recherche-form').on('submit', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: '/site/recherche', // URL vers l'action dans le controller
        type: 'GET',
        data: $(this).serialize(),  // Adapte le format JSON en string

        /* J'ai choisi JSON au lieu d'un renderPartial avec HTML car JSON est un format de données structurées en paires clé/valeur, 
        utilisé pour l'échange d'informations entre navigateurs et serveurs */
        dataType: 'json',   

        success: function(data) {

            // Mise à jour du contenu du bandeau de notification
            $('#notification')
                .text(data.notification)
                .removeClass('alert-success alert-danger')
                .addClass(
                    (data.notification === 'Plusieurs voyages ont été trouvés correspondants à votre recherche !' || 
                    data.notification === 'Un voyage a été trouvé correspondant à votre recherche !')
                        ? 'alert-success' 
                        : 'alert-danger'
                )
                .fadeIn();  // Permet au bandeau de notification d'arriver de manière smooth (proposé par JQuery)

            // Mise à jour des résultats obtenus
            $('#resultats').html(data.html);

        },

        error: function() {
            $('#notification')
            .text('Une erreur est survenue. Veuillez réessayer ultérieurement.')
            .addClass('alert-danger')
            .fadeIn();
        }

    });

});

// Soumission du formulaire de connexion
$('#login-form').on('submit', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: '/site/login', // URL vers l'action dans le controller
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',   // Adapte le format JSON en string

        success: function(data) {

            // Mise à jour du contenu du bandeau de notification
            $('#notification')
                .text(data.notification)
                .removeClass('alert-success alert-danger')
                .addClass(data.success ? 'alert-success' : 'alert-danger')
                .fadeIn();  // Permet au bandeau de notification d'arriver de manière smooth (proposé par JQuery)

            // Redirige l'utilisateur vers la page d'accueil au bout de 3 secondes
            if(data.success) {
                setTimeout(() => {
                    window.location.href = '/site/index';
                }, 5000);
            }

        },

        error: function() {
            $('#notification')
                .text('Une erreur est survenue. Veuillez réessayer ultérieurement.')
                .addClass('alert-danger')
                .fadeIn();
        }
    });
});

// Soumission du formulaire d'inscription
$('#registration-form').on('submit', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: '/site/inscription',   // URL vers l'action dans le controller
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',   // Adapte le format JSON en string

        success: function(data) {

            // Mise à jour du contenu du bandeau de notification
            $('#notification')
                .text(data.notification)
                .removeClass('alert-success alert-danger')
                .addClass(data.success ? 'alert-success' : 'alert-danger')
                .fadeIn();  // Permet au bandeau de notification d'arriver de manière smooth (proposé par JQuery)

            if (data.success) {
                setTimeout(() => {
                    window.location.href = '/site/index';
                }, 5000);
            }

        },

        error: function() {
            $('#notification')
                .text('Une erreur est survenue. Veuillez réessayer ultérieurement.')
                .addClass('alert-danger')
                .fadeIn();
        }
    });
});

// Soumission du formulaire pour proposer un voyage
$('#proposer-form').on('submit', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    $.ajax({
        url: '/site/proposer',   // URL vers l'action dans le controller
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',   // Adapte le format JSON en string

        success: function(data) {

            // Mise à jour du contenu du bandeau de notification
            $('#notification')
                .text(data.notification)
                .removeClass('alert-success alert-danger')
                .addClass('alert-success')
                .fadeIn();  // Permet au bandeau de notification d'arriver de manière smooth (proposé par JQuery)

            if(data.success) {
                $('#proposer-form')[0].reset(); // Réinitialise les champs du formulaire
                 $('html, body').animate({ scrollTop: 0 }, 'fast'); // Remonte tout en haut pour pouvoir voir le bandeau
                setTimeout(() => {
                    window.location.href = '/site/index';
                }, 5000);
            }

        },

        error: function() {
            $('#notification')
                .text('Une erreur est survenue. Veuillez réessayer ultérieurement.')
                .addClass('alert-danger')
                .fadeIn();
        }
    });
});


