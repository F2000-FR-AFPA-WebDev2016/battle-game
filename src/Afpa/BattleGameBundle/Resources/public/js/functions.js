$(document).ready(function () {
    console.log('JQuery OK');
    // 1. Au survol du menu, afficher "Survol du logo !" dans le
    // paragraphe "monParagraphe"
    // -- On récupère l'élément et on se connecte à l'évènement 'click'
    $('#game').on('click', '.board_shoot td', function () {
        selected_case = $(this);

        $.ajax({
            async: true,
            type: 'POST',
            url: START_URL + '/game/action',
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
        // selected_case = $(this);
        $.ajax({
            async: true,
            type: 'POST',
            url: START_URL + '/game/view',
            error: function (errorData) {
                console.log(errorData);
            },
            success: function (data) {
                $('#game').html(data);
            }
        });
    }

    // auto-refresh
    setInterval(refresh, 2000);
});