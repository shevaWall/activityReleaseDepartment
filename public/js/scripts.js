$(document).ready(function () {
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
            .append("<a class='d-block' href='" + item.url + "'><div>" + item.label + "</div></a>")
            .appendTo(ul);
    };

    // ajax подзагрузка списка транзацкий при прокрутке
    $(document).on('scroll', function () {
        let ajaxMoreTransactions = $('.ajaxMoreTransactions');
        if ($(ajaxMoreTransactions).length !== 0) {

            let ajaxMoreTransactionsOffset = $(ajaxMoreTransactions).offset().top;
            let documentScroll = $(document).scrollTop() + $(window).height();
            let insertAjaxTransaction = $('.transactionsTable').children('tbody');
            let lastTransactionId = $(document).find('.transactionsTable').children('tbody').children('tr').last().attr('data-transaction-id');

            if (documentScroll >= ajaxMoreTransactionsOffset && !$(ajaxMoreTransactions).hasClass('ajaxOngoing')) {
                $.ajax({
                    type: 'get',
                    url: '/warehouse/ajaxMoreTransaction/' + lastTransactionId,
                    beforeSend: function () {
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

    // вкладки для страницы "расход бумаги"
    $('#paperConsumptionTabs, #paperConsumptionTabsSubPivotTable, #paperConsumptionTabsSubPD, #paperConsumptionTabsSubRD, #paperConsumptionTabsSubII').tabs();

    // ajax добавление записи в блокнот на главной странице
    $('#newNote').on('keydown', function (e) {
        // если нажатие на Enter, но без зажатого shift, то отпраляем запрос. Если с зажатым - игнориемум, чтобы был
        // перенос строки
        if (e.key == 'Enter' && !e.shiftKey) {
            let form_data = new FormData();
            form_data.append('_token', $('#notesForm').find("input[name='_token']").val());
            form_data.append('name', $("#notesForm").find('textarea').val());

            $.ajax({
                url: '/blocknotes/addNote',
                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (newNote) {
                    $('#notesForm').find('textarea').val('');
                    $('.note-list').append(newNote);
                }
            });
        }
    });
});

// обработчик события сброса файлов в браузер в зону сброса
function dndDropMany(element, token) {
    stopPreventDef();
    let files = window.event.dataTransfer.files;
    files = Object.entries(files);
    if (files.length > 0) {
        $(element).find('.fieldForDropText').addClass('d-none');
        $(element).find('.fieldForDropCount').removeClass('d-none');
        $(element).find('.fieldForDropCount #current').text('1');
        $(element).find('.fieldForDropCount #all').text(files.length);
        ajaxCountFormats(element, files, token);
    }
}

// обработчик события наведения файлов в браузере в зону сброса
function dndDragenter(element) {
    // $(element).find('.fieldForDropText').addClass('invisible');
    $(element).addClass('hovered');
}

// обработчик обратного действия dndDragenter
function dndDragleave(element) {
    $(element).find('.fieldForDropText').removeClass('invisible');
    $(element).removeClass('hovered');
}

// отменяет все действия по умолчанию для браузера при перемещении файлов в браузер
function stopPreventDef() {
    window.event.stopPropagation();
    window.event.preventDefault();
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
            // console.log(msg);
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
function ajaxCountFormats(element, files, token) {
    if (typeof files !== 'undefined' && files.length > 0) {
        let fileName = files[0][1].name;
        let fileNameParts = fileName.split('.');
        let fileExtension = fileNameParts[fileNameParts.length - 1].toLowerCase();
        let newFileName = '';
        let objectId = $(element).data('object-id');
        let compositGroup_id = $(element).data('composit-group-id');

        if (fileExtension === 'pdf') {
            // объединяем части от имени файла в одну переменную, но без расширения файла
            for (let i = 0; i < fileNameParts.length - 1; i++) {
                newFileName += fileNameParts[i];
            }

            // подготавливаем данные для создания состава (раздела)
            let compositFormData = new FormData();
            compositFormData.append('name', newFileName);
            compositFormData.append('object_id', objectId);
            compositFormData.append('compositGroup_id', compositGroup_id);
            compositFormData.append('_token', token);

            $.ajax({
                url: '/composit/ajaxAddComposit',
                data: compositFormData,
                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                type: 'post',
                success: function (newComposit) {
                    $('tbody#compositGroup_' + compositGroup_id).append(newComposit);
                    let composit_id = $('tbody#compositGroup_' + compositGroup_id).children('tr:last').data('composit-id');
                    // подготавливаем данные для подсчета pdf
                    let pdfFormData = new FormData();
                    pdfFormData.append('_token', token);
                    pdfFormData.append('pdf', files[0][1]);
                    pdfFormData.append('composit_id', composit_id);

                    $.ajax({
                        url: '/countPdf/ajaxCountPdf/' + composit_id,
                        dataType: 'text',
                        cache: false,
                        mimeType: "multipart/form-data",
                        contentType: false,
                        processData: false,
                        data: pdfFormData,
                        type: 'post',
                        beforeSend: function () {
                            // отображаем спиннер
                            $('#compositId_' + composit_id).find('.spinner-border').toggleClass('d-none');
                            // удаляем таблицу с форматами, для отображения новой таблицы из ajax ответа
                            $('#compositId_' + composit_id).find('.newTableHere').find('table').remove();
                        },
                        success: function (msg) {
                            // получаем новые обработанные данные страниц
                            $.ajax({
                                type: 'get',
                                url: '/countPdf/ajaxGetCountedPdf/' + composit_id,
                                success: function (msg) {
                                    // отображаем новые обработанные форматы страниц
                                    $('#compositId_' + composit_id).find('.newTableHere').append(msg);
                                    // скрываем спиннер
                                    $('#compositId_' + composit_id).find('.spinner-border').toggleClass('d-none');
                                    files.shift();
                                    if (files.length > 0) {
                                        // увеличиваем счетчик "обработка"
                                        $(element).find('#current').text(parseInt($(element).find('#current').text()) + 1);
                                    }
                                    ajaxCountFormats(element, files, token);
                                }
                            });
                        },
                        error: function (msg) {
                            // console.log(msg.status);
                            // $('#response').html(msg.responseText);
                            let badPdf_modal = new bootstrap.Modal(document.getElementById('badPdf_modal'));

                            // выключаем спиннер
                            $('#compositId_' + composit_id).find('.spinner-border').toggleClass('d-none');

                            if (msg.status === 504) {
                                $('#compositId_' + composit_id).find('.newTableHere').text('Для этого файла нужно больше времени на обработку. ' +
                                    'Процесс всё ещё идёт в фоновом режиме. Попробуйте перезагрузить страницу позже.');
                                files.shift();
                                if (files.length > 0) {
                                    // увеличиваем счетчик "обработка"
                                    $(element).find('#current').text(parseInt($(element).find('#current').text()) + 1);
                                }
                                ajaxCountFormats(element, files, token);
                            } else {
                                let error = "<li>Произошла ошибка. Файл: "+fileName+". Попробуйте восстановить файл через <a href='https://www.ilovepdf.com/repair-pdf'>I Love Pdf <3</a></li>";
                                $(element).siblings('ul').append(error);

                                files.shift();
                                if (files.length > 0) {
                                    // увеличиваем счетчик "обработка"
                                    $(element).find('#current').text(parseInt($(element).find('#current').text()) + 1);
                                }
                                ajaxCountFormats(element, files, token);
                            }
                        }
                    });
                }
            })
        }else{
            let error = "<li>Не правильное расширение файла: "+fileName+"</li>";
            $(element).siblings('ul').append(error);
            files.shift();
            if (files.length > 0) {
                // увеличиваем счетчик "обработка"
                $(element).find('#current').text(parseInt($(element).find('#current').text()) + 1);
            }
            ajaxCountFormats(element, files, token);
        }
    } else {
        $(element).find('.fieldForDropCount').addClass('d-none');
        $(element).find('.fieldForDropText').removeClass('d-none');
    }

    /*if (fileExtension === 'pdf') {
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
                // console.log(msg.status);
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
                // console.log(msg);
                $('#response').html(msg);
                let badPdfExtension = new bootstrap.Modal(document.getElementById('badPdfExtension'));
                badPdfExtension.toggle();
                badPdfExtension._element.addEventListener('hide.bs.modal', function (event) {
                    $('#response').html('');
                });
            }
        });
    }*/
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
            // console.log(msg);
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

// ajax удаление заметки на главной странице
function ajaxDeleteNote(note_id, token) {
    let form_data = new FormData();
    form_data.append('id', note_id);
    form_data.append('_token', token);
    $.ajax({
        url: '/blocknotes/deleteNote',
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function () {
            $('.notes').find(".row[data-note-id=" + note_id + "]").remove();
        }
    });
}
