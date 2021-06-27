export function print(document, code) {
    bootbox.dialog({
        message: '<h4>¿Qué típo de impresión deseas?</h4>',
        size: 'medium',
        buttons: {
            gift: {
                label: '<i class="fas fa-gift"></i> Regalo',
                className: 'btn-warning',
                callback: function(){
                    printDialog(code, document, 1);
                }
            },
            normal: {
                label: '<i class="fas fa-print"></i> Normal',
                className: 'btn-primary',
                callback: function(){
                    printDialog(code, document, 0);
                }
            }
        }
    });
}

function printDialog(code, document, gift = 0) {
    const PRINTER_SERVER_PORT = '8089';

    const data = {
        code: code,
        documento: document,
        gift: gift
    };

    $.ajax({
        type: 'POST',
        url: 'PrintTicket',
        data: data,
        success: function () {
            var dialog = bootbox.dialog({
                message: '<div class="text-center"><h1><i class="fas fa-print" aria-hidden="true"></i></h1></div>',
            }).on('shown.bs.modal', function(){
                $.ajax({
                    url: 'http://localhost:' + PRINTER_SERVER_PORT + '?documento=' + data.documento,
                    type: 'GET',
                    crossDomain: true,
                    success: function () {
                        setTimeout(function(){
                            dialog.modal('hide');
                        }, 400);
                    }
                });
            });
        }
    });
}