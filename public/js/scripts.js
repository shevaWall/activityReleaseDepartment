$(document).ready(function () {

    // ajax добавление раздела (состава)
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
                recountPersents($(o_form).find('tbody'));
            }
        });
    });
});

// ajax изменение состояния объекта
function ajaxChangeObjectStatus(element) {
    let tr = $(element).parents('tr');
    $(tr).fadeOut();
    // $(tr).addClass('d-none');
    let status_id = $(element).find('option:selected').val()
    let object_id = parseInt($(element).attr('id').replace(/\D+/g, ""));
    let url = "/objects/changeObjectStatus/"+object_id+"/"+status_id;

    $.ajax({
        type: 'get',
        url: url,

        success: function(msg){
            console.log(msg);
        }
    });
}
// ajax удаление элемента раздела(состава)
function ajaxDeleteComposit(composit){
    let removedTr   = $(composit).parents('tr');
    let compositId  = parseInt($(removedTr).attr('id').replace(/\D+/g,""));
    let url         = "/composit/ajaxDeleteComposit/"+compositId;

    $.ajax({
        type: 'get',
        url: url,

        success: function(msg){
            let tbody = $(removedTr).parents('tbody')
            $(removedTr).remove();
            recountPersents(tbody);
        }
    });
}

// ajax изменение статуса раздела
function ajaxCompositChangeStatus(composit){
    let composit_id = parseInt($(composit).parents('tr').attr('id').replace(/\D+/g,""));
    let url = "/composit/ajaxChangeCompositStatus/"+composit_id;

    $.ajax({
        type: 'get',
        url: url,

        success: function(msg){
            $(composit).text(msg);
            $(composit).toggleClass('completed uncompleted');
            recountPersents($(composit).parents('tbody'));
        }
    });
}

// перерасчет процентного соотношения выполненой печати для группы разделов
function recountPersents(compositGroup){
    let compositGroupId         = parseInt($(compositGroup).attr('id').replace(/\D+/g,""));
    let uncompletedComposits    = $(compositGroup).find('p.uncompleted').length;
    let completedComposits      = $(compositGroup).find('p.completed').length;
    if(!(uncompletedComposits === 0 && completedComposits === 0)){
        let persents                = parseInt(completedComposits/(uncompletedComposits+completedComposits)*100);
        $('span#compositGroupPersents_'+compositGroupId).text(persents);
    }else{
        $('span#compositGroupPersents_'+compositGroupId).text(0);
    }
}


// аякс подсчет страниц pdf
function ajaxCountFormats(element) {
    let file_data = $(element).prop('files')[0];
    let form_data = new FormData();
    let composit_id = parseInt($(element).parents('tr').attr('id').replace(/\D+/g,""));
    let error_pdf = $(element).parents('tr').find('.error_pdf');

    form_data.append('pdf', file_data);
    form_data.append('_token', $(element).parents('form').find("input[name='_token']").val());

    $.ajax({
        url: '/countPdf/ajaxCountPdf/'+composit_id,
        dataType: 'text',
        cache: false,
        mimeType: "multipart/form-data",
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function(){
            $(element).siblings("input[type='button']").prop("disabled", true);

            $(element).parents('tr').find('.formatsTable').remove();
            $(element).parents('tr').find('.spinner-border').toggleClass('d-none');
            if(!$(error_pdf).hasClass('d-none')){
                $(error_pdf).toggleClass('d-none');
            }
        },
        success: function (msg) {
            $(element).siblings("input[type='button']").prop("disabled", false);
            // todo: при success почему-то сбрасывает для всех форм
            $(element).parents('form')[0].reset();

            $.ajax({
                type: 'get',
                url: '/countPdf/ajaxGetCountedPdf/'+composit_id,
                success: function(msg){
                    $(element).parents('tr').find('.newTableHere').append(msg);
                    $(element).parents('tr').find('.spinner-border').toggleClass('d-none');
                },
                error: function(msg){
                    $('#response').append(msg);
                }
            });
        },
        error: function(msg){
            $('#response').html(msg.responseText);
            let badPdf_modal = new bootstrap.Modal(document.getElementById('badPdf_modal'));

            $(element).siblings("input[type='button']").prop("disabled", false);
            // todo: при success почему-то сбрасывает для всех форм
            $(element).parents('form')[0].reset();
            $(element).parents('tr').find('.spinner-border').toggleClass('d-none');

            if($(error_pdf).hasClass('d-none'))
                $(error_pdf).toggleClass('d-none');

            badPdf_modal.toggle();
            badPdf_modal._element.addEventListener('hide.bs.modal', function(event){
                $('#response').html('');
            });
        }
    });
}

/**
 * сбрасываем посчитанные страницы PDF у определенного раздела (состава)
 */
function ajaxCompositRefresh(element){
    let composit_id = parseInt($(element).parents('tr').attr('id').replace(/\D+/g,""));
    let tableTbody = $(element).parents('tr').find('.formatsTable tbody');

    $.ajax({
        type: 'get',
        url: '/countPdf/ajaxDropCounted/'+composit_id,

        success: function(msg){
            if($(tableTbody).find('tr').length > 0){
                $(tableTbody).find('tr').remove();
            }
        }
    });
}
