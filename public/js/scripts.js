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
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
            .append("<div><a href='" + item.url + "'>" + item.label + "</a></div>")
            .appendTo(ul);
    };

    // ajax подзагрузка списка транзацкий при прокрутке
    $(document).on('scroll', function () {
        let ajaxMoreTransactions = $('.ajaxMoreTransactions');
        if($(ajaxMoreTransactions).length !== 0){

            let ajaxMoreTransactionsOffset = $(ajaxMoreTransactions).offset().top;
            let documentScroll = $(document).scrollTop() + $(window).height();
            let insertAjaxTransaction = $('.transactionsTable').children('tbody');
            let lastTransactionId = $(document).find('.transactionsTable').children('tbody').children('tr').last().attr('data-transaction-id');

            if (documentScroll >= ajaxMoreTransactionsOffset && !$(ajaxMoreTransactions).hasClass('ajaxOngoing')) {
                $.ajax({
                    type: 'get',
                    url: '/warehouse/ajaxMoreTransaction/' + lastTransactionId,
                    beforeSend: function(){
                        $(ajaxMoreTransactions).addClass('ajaxOngoing');
                    },
                    success: function (msg) {
                        $(insertAjaxTransaction).append(msg);
                        $(ajaxMoreTransactions).removeClass('ajaxOngoing');
                    }
                });
            }
        }
    });
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
function dblclick_renameComposit(element) {
    let inpt = $(element).siblings('.renameComposit');
    $([element, inpt]).toggleClass('d-none');
}

function completeRenameComposit(element) {
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

// редактирование материала актуального склада
function editWarehouseItem(element) {
    let row = $(element).parents('tr');
    let warehouseId = $(row).attr('data-warehouse-item');
    let a_warehouseText = $(row).find('.warehouse-text');
    let a_inputs = $(row).find('input');
    let token = $(element).parents('tbody').find("input[name='_token']").val();
    let changedFlag = false;
    let notEmptyField = true;

    // срабатывает, если input был виден и его предстоит скрыть
    if (!$(a_inputs[0]).hasClass('d-none')) {
        for (let i = 0; i < a_inputs.length; i++) {
            // если есть изменения, то меняем флаг
            if ($(a_warehouseText[i]).text() !== $(a_inputs[i]).val())
                changedFlag = true;

            $(a_warehouseText[i]).text($(a_inputs[i]).val());

            // если какое-то поле пустое - не пропускаем на аякс
            if ($(a_warehouseText[i]).text() == '' || $(a_inputs[i]).val() == '')
                notEmptyField = false;
        }

        // подготавливаем и отправляем данные о изменении данных на актуальном складе,
        // отправляем только если были изменения
        if (changedFlag && notEmptyField) {
            let form_data = new FormData();
            form_data.append(' id', warehouseId);
            form_data.append('material', $(a_warehouseText[0]).text());
            form_data.append('quantity', $(a_warehouseText[1]).text());
            form_data.append('_token', token);

            $.ajax({
                type: 'post',
                url: '/warehouse/updateWarehouseActualData/',
                dataType: 'text',
                cache: false,
                mimeType: "multipart/form-data",
                contentType: false,
                processData: false,
                data: form_data,

                success: function (msg) {
                    // $('.response').html(msg);
                    // добавляем запись в таблицу транзакций
                    let tbody = $('.transactionsTable').find('tbody');

                    if ($(tbody).find('tr').length === 0) {
                        $(tbody).append(msg);
                    } else {
                        $(tbody).find('tr').first().before(msg);
                    }

                    // показываем кнопку добавление нового материала (см function addWarehouseItem())
                    $('.addCircleBtn').removeClass('d-none');
                }
            });
        }
    }

    // если после редактирования нет пустых полей, то переключаемся.
    if (notEmptyField) {
        $(row).find('.warehouse-text, input').toggleClass('d-none');

        // фокусируемся на первом инпуте для удобства
        if (!$(row).find('input').hasClass('d-none')) {
            let focusOnInput = $(row).find('input').first();
            focusOnInput.focus();
            focusOnInput[0].selectionStart = focusOnInput.val().length;

            // добавляем autocomplete, чтобы исключить очепятки
            let availableMaterials = [
                "Бумага А4",
                "Бумага А3",
                "Рулонная бумага 841",
                "Рулонная бумага 594",
                "Рулонная бумага 420",
                "Рулонная бумага 297",
                "Пластик",
                "Картон",
                "CD",
                "DVD",
                "Короб архивный"
            ];
            $(focusOnInput).autocomplete({
                source: availableMaterials,
            })
        }
    } else {
        // если есть пустое поле - фокусируемся на нём
        $.each($(row).find('input'), function (i, element) {
            if ($(element).val() == '') {
                $(element).focus();
            }
        });
    }
}

// добавление нового материала в актуальный склад
function addWarehouseItem(element) {
    let tbody = $(element).parents('tbody');

    $(tbody).find('tr:last').before(function () {
        $.ajax({
            url: '/warehouse/ajaxAddNewTr/',
            type: 'get',
            success: function (msg) {
                let lastTr = $(tbody).find('tr').last();
                $(lastTr).before(msg);
            }
        })
    });

    // скрываем кнопку добавление нового материала, чтобы не получалось так, что создают много пустых строк и
    // можно было очевидно присвоить айди нового материала к конкретной строке
    $('.addCircleBtn').addClass('d-none');
}

// удаление материала в актуальном складе
function deleteWarehouseItem(element) {
    let tr = $(element).parents('tr');
    let warehouseItem_id = $(tr).attr('data-warehouse-item');

    if (warehouseItem_id !== '0') {
        $.ajax({
            url: '/warehouse/ajaxDeleteItem/' + warehouseItem_id,
            type: 'get',
            success: function (msg) {
                $('.transactionsTable').find('tbody').find('tr').first().before(msg);
                // console.log(msg);
                $(tr).remove();
            }
        })
    } else {
        $(tr).remove();
        // если сначала нажали на кнопку добавление нового материала, а потом вдруг передумали и решили удалить эту строку
        if ($('.addCircleBtn').hasClass('d-none'))
            $('.addCircleBtn').removeClass('d-none');
    }
}


// кнопка "показать итог" на странице сводной информации о количестве листов
function showTotalPaperConsumption(element) {
    switch ($(element).text()) {
        case 'Показать итого':
            $(element).text('Скрыть итого');
            break;
        case 'Скрыть итого':
            $(element).text('Показать итого');
            break;
    }
    $('.tablePaperConsumption').find('.toggleTotal').toggleClass('d-none');
}
