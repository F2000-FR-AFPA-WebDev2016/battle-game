$(document).ready(function () {
    console.log('JQuery OK');

    /**
     * Récupérer le data-id de l'élément #game
     * @returns int
     */
    function getGameId() {
        return $('#boards').data('id');
    }


    // 1. Au survol du menu, afficher "Survol du logo !" dans le
    // paragraphe "monParagraphe"
    // -- On récupère l'élément et on se connecte à l'évènement 'click'
    $('#game').on('click', '.board_shoot td', function () {
        selected_case = $(this);

        // gestion du mode offline/online
        url = START_URL + '/game/action';
        idGame = getGameId();
        if (idGame) {
            url += '/' + idGame;
        }

        $.ajax({
            async: true,
            type: 'POST',
            url: url,
            data: {
                x: $(this).data('x'),
                y: $(this).data('y'),
            },
            error: function (errorData) {
                console.log(errorData);
            },
            success: function (data) {
                console.log(data);
                selected_case.css('background', 'red');
                refresh();
            }
        });

        // console.log($(this).data('x') + ' ' + $(this).data('y'));
    });

    // rafraichir la vue partielle
    function refresh() {
        // gestion du mode offline/online
        url = START_URL + '/game/refresh';
        idGame = getGameId();
        if (idGame) {
            url += '/' + idGame;
        }

        // selected_case = $(this);
        $.ajax({
            async: true,
            type: 'POST',
            url: url,
            error: function (errorData) {
                console.log(errorData);
            },
            success: function (data) {
                $('#game').html(data);
            }
        });
    }

    // auto-refresh si jeu présent sur la page
    if ($('#boards').length > 0) {
        setInterval(refresh, 2000);
    }
});