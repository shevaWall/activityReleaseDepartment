$(document).ready(function(){

    // ajax добавление раздела документации
    $('.ajaxSend').on('click', function(e){
        e.preventDefault();
        let o_form = $(this).parents('form');
        console.log($(o_form).serialize());

        $.ajax({
            type		: $(o_form).attr('method'),
            url			: $(o_form).attr('action'), // ссылка на страницу с формой
            data		: $(o_form).serialize(),

            success		: function(msg){
                // todo: добавить красивую анимацию popup'a о добавлении раздела
                // $('#response').append(msg);

            }
        });
    });
});
