$(document).ready(function () {

    // ajax добавление раздела
    $('.ajaxSend').on('click', function (e) {
        e.preventDefault();
        let o_form = $(this).parents('form');

        $.ajax({
            type: $(o_form).attr('method'),
            url: $(o_form).attr('action'), // ссылка на страницу с формой
            data: $(o_form).serialize(),

            success: function (msg) {
                $jsonMsg = jQuery.parseJSON(msg);

                // todo: добавить красивую анимацию popup'a о добавлении раздела

                /*todo: сделать обновление колонки с составом, потому что если нет ни одного элемента, то ничего не
                добавляется. И если что-то удаляется - всё-равно нужно переобновлять весь список (хотя можно посмотреть как это сделать через html/css)*/

                let tbody = $(o_form).find("tbody");
                let lastTr = $(tbody).find('tr').last().prev();
                let newTr = $(lastTr).clone();

                $(newTr).find('td').first().text($jsonMsg.name);
                $(newTr).find('th').text(parseInt($(newTr).find('th').text()) + 1);
                $(lastTr).after(newTr);

                $(o_form).find('input[name="name"]').val('');

                // $('#response').append($jsonMsg);
            }
        });
    });

    // ajax удаление раздела
    $('.ajaxDeleteComposit').on('click', function (e) {
        e.preventDefault();

        let removedTr = $(this).parents('tr');

        $.ajax({
            type: 'get',
            url: $(this).attr('href'),

            success: function(msg){
                // todo: а что если пытаются удалить элемент, которого нет?
                $(removedTr).remove();
            }

        });
    });


    // ajax изменение статуса раздела
    $("p[id^='compositId_']").on('click', function(e) {
        let composit = $(this);
        let composit_id = parseInt($(composit).attr('id').replace(/\D+/g,""));
        let url = "/composit/ajaxChangeCompositStatus/"+composit_id;

        $.ajax({
            type: 'get',
            url: url,

            success: function(msg){
                composit.text(msg);
                composit.toggleClass('completed uncompleted');
            }
        });
    });

});
