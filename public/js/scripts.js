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
                let tbody = $(o_form).find("tbody");
                let lastTr = $(tbody).find('tr').last();

                $(lastTr).before(msg);
                $(o_form).find('input[name="name"]').val('');
                recountPersents($(o_form).find('tbody'));
            }
        });
    });

    // ajax поиск по названию объекта или по номеру заявки
    $("#searchObject").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "/search",
                dataType: "json",
                data: {
                    term: request.term,
                    ajax: true
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        minLength: 2
    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
        console.log(item);
        return $("<li>")
            .append("<div><a href='"+item.url+"'>" + item.label+"</a></div>")
            .appendTo(ul);
    };

});


function dndDrop(element) {
    stopPreventDef();

    let input = $(element).siblings('input[type="file"]');
    let files = window.event.dataTransfer.files;

    $(element).removeClass('highlight');
    if (files.length > 1) {
        $(element).text('Сюда можно загрузить только один файл! Отмена обработки.');
    } else {
        $(element).text('Обработка ...');
        ajaxCountFormats(element, files[0]);
    }
}

function dndDragenter(element) {
    $(element).addClass('highlight');
    $(element).text('Отпустите, чтобы загрузить файл.');
}

function dndDragleave(element) {
    $(element).removeClass('highlight');
    $(element).text('Для загрузки, перетащите файл сюда или нажмите здесь.');
}

function stopPreventDef() {
    window.event.stopPropagation();
    window.event.preventDefault();
}

// для загрузки DnD файлов. Вызов проводника при клике на область
function openFileExplorer(element) {
    let compositId = parseInt($(element).parents('tr').attr('id').replace(/\D+/g, ""));
    let input = $('#countPdf_' + compositId);
    $(input).click();
}

// ajax изменение состояния объекта
function ajaxChangeObjectStatus(element) {
    let tr = $(element).parents('tr');
    $(tr).fadeOut();
    let status_id = $(element).find('option:selected').val()
    let object_id = parseInt($(element).attr('id').replace(/\D+/g, ""));
    let url = "/objects/changeObjectStatus/" + object_id + "/" + status_id;

    $.ajax({
        type: 'get',
        url: url,

        success: function (msg) {
            console.log(msg);
        }
    });
}

// ajax удаление элемента раздела(состава)
function ajaxDeleteComposit(composit) {
    let removedTr = $(composit).parents('tr');
    let compositId = parseInt($(removedTr).attr('id').replace(/\D+/g, ""));
    let url = "/composit/ajaxDeleteComposit/" + compositId;

    $.ajax({
        type: 'get',
        url: url,

        success: function (msg) {
            let tbody = $(removedTr).parents('tbody')
            $(removedTr).remove();
            recountPersents(tbody);
        }
    });
}

// ajax изменение статуса раздела
function ajaxCompositChangeStatus(composit) {
    let composit_id = parseInt($(composit).parents('tr').attr('id').replace(/\D+/g, ""));
    let url = "/composit/ajaxChangeCompositStatus/" + composit_id;

    $.ajax({
        type: 'get',
        url: url,

        success: function (msg) {
            $(composit).text(msg);
            $(composit).toggleClass('completed uncompleted');
            recountPersents($(composit).parents('tbody'));
        }
    });
}

// перерасчет процентного соотношения выполненой печати для группы разделов
function recountPersents(compositGroup) {
    let compositGroupId = parseInt($(compositGroup).attr('id').replace(/\D+/g, ""));
    let uncompletedComposits = $(compositGroup).find('p.uncompleted').length;
    let completedComposits = $(compositGroup).find('p.completed').length;
    if (!(uncompletedComposits === 0 && completedComposits === 0)) {
        let persents = parseInt(completedComposits / (uncompletedComposits + completedComposits) * 100);
        $('span#compositGroupPersents_' + compositGroupId).text(persents);
    } else {
        $('span#compositGroupPersents_' + compositGroupId).text(0);
    }
}

// аякс подсчет страниц pdf
function ajaxCountFormats(element, file) {
    let fileName = file.name;
    let fileNameParts = fileName.split('.');
    let fileExtension = fileNameParts[fileNameParts.length - 1].toLowerCase();

    let form_data = new FormData();
    let composit_id = parseInt($(element).parents('tr').attr('id').replace(/\D+/g, ""));

    let error_pdf = $(element).parents('tr').find('.error_pdf');

    // добавляем в FormData файл pdf и токен
    form_data.append('pdf', file);
    form_data.append('_token', $(element).parents('form').find("input[name='_token']").val());

    if (fileExtension === 'pdf') {
        $.ajax({
            url: '/countPdf/ajaxCountPdf/' + composit_id,
            dataType: 'text',
            cache: false,
            mimeType: "multipart/form-data",
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            beforeSend: function () {
                // очищаем поле с таблицей под новые данные
                $(element).parents('tr').find('.formatsTable').remove();
                // показываем спиннер, чтобы было видно, что процесс идёт
                $(element).parents('tr').find('.spinner-border').toggleClass('d-none');
                // если до этого срабатывал флаг на ошибку загрузки файла, то прячем его
                if (!$(error_pdf).hasClass('d-none')) {
                    $(error_pdf).toggleClass('d-none');
                }
            },
            success: function (msg) {
                // сбрасываем скрытый input для загрузки файлов (если файл был загружен через проводник)
                $(element).siblings('input[type="file"]').val('');

                // получаем новые обработанные данные страниц
                $.ajax({
                    type: 'get',
                    url: '/countPdf/ajaxGetCountedPdf/' + composit_id,
                    success: function (msg) {
                        // отображаем новые обработанные форматы страниц
                        $(element).parents('tr').find('.newTableHere').append(msg);
                        // выключаем спиннер
                        $(element).parents('tr').find('.spinner-border').toggleClass('d-none');
                        // возвращаем надпись в DnD блоке в исходное состояние
                        $(element).text('Успешно!');
                    },
                    error: function (msg) {
                        $('#response').append(msg);
                    }
                });
            },
            error: function (msg) {
                console.log(msg.status);
                $('#response').html(msg.responseText);
                let badPdf_modal = new bootstrap.Modal(document.getElementById('badPdf_modal'));

                // сбрасываем скрытый input для загрузки файлов (если файл был загружен через проводник)
                $(element).siblings('input[type="file"]').val('');
                // выключаем спиннер
                $(element).parents('tr').find('.spinner-border').toggleClass('d-none');

                if ($(error_pdf).hasClass('d-none')) {
                    $(error_pdf).toggleClass('d-none');
                    if (msg.status === 504) {
                        $(element).text('Для этого файла нужно больше времени на обработку. ' +
                            'Процесс всё ещё идёт в фоновом режиме. Попробуйте перезагрузить страницу позже.');
                    } else {
                        $(element).text('Произошла ошибка');

                        // отображаем всплывашку ошибки
                        badPdf_modal.toggle();

                        badPdf_modal._element.addEventListener('hide.bs.modal', function (event) {
                            $('#response').html('');
                        });
                    }
                }
            }
        });
    } else {
        $.ajax({
            type: 'get',
            url: '/countPdf/ajaxBadExtension',

            success: function (msg) {
                console.log(msg);
                $('#response').html(msg);
                let badPdfExtension = new bootstrap.Modal(document.getElementById('badPdfExtension'));
                badPdfExtension.toggle();
                badPdfExtension._element.addEventListener('hide.bs.modal', function (event) {
                    $('#response').html('');
                });
            }
        });
    }
}

// ajax сбрасываем посчитанные страницы PDF у определенного раздела (состава)
function ajaxCompositRefresh(element) {
    let composit_id = parseInt($(element).parents('tr').attr('id').replace(/\D+/g, ""));
    let tableTbody = $(element).parents('tr').find('.formatsTable tbody');

    $.ajax({
        type: 'get',
        url: '/countPdf/ajaxDropCounted/' + composit_id,

        success: function (msg) {
            if ($(tableTbody).find('tr').length > 0) {
                $(tableTbody).find('tr').remove();
            }
        }
    });
}

// для переименовывания элемента
function dblclick_renameComposit(element){
    let inpt = $(element).siblings('.renameComposit');
    $([element, inpt]).toggleClass('d-none');
}
function completeRenameComposit(element){
    let composit_id = parseInt($(element).parents('tr').attr('id').replace(/\D+/g, ""));
    let cursorRenameComposit = $(element).parent('td').siblings('.cursorRenameComposit');
    let inpt = $(element).parent('td');

    $([cursorRenameComposit, inpt]).toggleClass('d-none');
    $(cursorRenameComposit).text($(inpt).find('input').val());

    let form_data = new FormData();
    form_data.append('name', $(inpt).find('input').val());
    form_data.append('_token', $(element).parents('form').find("input[name='_token']").val());

    $.ajax({
        type: 'post',
        url: '/composit/ajaxRenameComposit/' + composit_id,
        dataType: 'text',
        cache: false,
        mimeType: "multipart/form-data",
        contentType: false,
        processData: false,
        data: form_data,

        success: function (msg) {
            console.log(msg);
        }
    });
}
