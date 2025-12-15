// Soumission du formulaire de recherche d'un voyage
$('#recherche-form').on('submit', function(e) {

    e.preventDefault(); // Empêche de recharger entièrement la page (= contradictoire avec Ajax)

    // "Appel à la méthode $.ajax()"
    $.ajax({
        url: '/site/recherche', // URL vers l'action dans le controller
        type: 'GET',
        data: $(this).serialize(),  // adapte le format JSON en string

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
                .fadeIn();  // permet d'arriver de manière smooth (proposé par Jquery)

            // Mise à jour des résultats obtenus
            $('#resultats').html(data.html);

            if(data.errors) console.log(data.errors);   // affichage des erreurs dans la console

        },

        error: function() {
            $('#notification').text('Une erreur est survenue.');
        }

    });

});

// Soumission du formulaire de connexion
$('#login-form').on('submit', function(e) {

    e.preventDefault();

    $.ajax({
        url: '/site/login',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function(data) {

            $('#notification')
                .text(data.notification)
                .removeClass('alert-success alert-danger')
                .addClass(data.success ? 'alert-success' : 'alert-danger')
                .fadeIn();

            // Redirige l'utilisateur vers la page d'accueil au bout de 3 secondes
            if(data.success) {
                setTimeout(() => {
                    window.location.href = '/';
                }, 3000);
            }

        },

        error: function() {
            $('#notification')
                .text('Erreur serveur.')
                .addClass('alert-danger')
                .fadeIn();
        }
    });
});

// Soumission du formulaire d'inscription
$('#registration-form').on('submit', function(e) {

    e.preventDefault();

    $.ajax({
        url: '/site/inscription',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',

        success: function(data) {

            $('#notification')
                .text(data.notification)
                .removeClass('alert-success alert-danger')
                .addClass(data.success ? 'alert-success' : 'alert-danger')
                .fadeIn();

            if (data.success) {
                setTimeout(() => {
                    window.location.href = '/';
                }, 1500);
            }

            if (data.errors) console.log(data.errors);
        },

        error: function() {
            $('#notification')
                .text('Erreur serveur.')
                .addClass('alert-danger')
                .fadeIn();
        }
    });
});


