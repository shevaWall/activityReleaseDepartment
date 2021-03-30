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
                // $('#response').append(msg);

                // todo: добавить красивую анимацию popup'a о добавлении раздела
                let tbody   = $(o_form).find("tbody");
                let lastTr  = $(tbody).find('tr').last();

                $(lastTr).before(msg);
                $(o_form).find('input[name="name"]').val('');
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
});


// ajax изменение статуса раздела
function ajaxCompositChangeStatus(composit){
    let composit_id = parseInt($(composit).attr('id').replace(/\D+/g,""));
    let url = "/composit/ajaxChangeCompositStatus/"+composit_id;

    $.ajax({
        type: 'get',
        url: url,

        success: function(msg){
            $(composit).text(msg);
            $(composit).toggleClass('completed uncompleted');
        }
    });
}
