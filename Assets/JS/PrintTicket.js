export function print(document, code) {
    bootbox.dialog({
        message: '<h4>¿Qué típo de impresión deseas?</h4>',
        size: 'medium',
        buttons: {
            gift: {
                label: '<i class="fas fa-gift"></i> Regalo',
                className: 'btn-warning',
                callback: function(){
                    sendPrintJob(this, code, document, 1);
                    return false;
                }
            },
            normal: {
                label: '<i class="fas fa-print"></i> Normal',
                className: 'btn-primary',
                callback: function(){
                    sendPrintJob(this, code, document, 0);
                    return false;
                }
            }
        }
    });
}

function sendPrintJob(dialog, code, document, gift = 0) {
    const data = {
        code: code,
        documento: document,
        gift: gift
    };

    $.ajax({
        type: 'POST',
        url: 'PrintTicket',
        dataType: 'json',
        data: data,
        success: function (response) {
            var html = [
                '<div class="row">',
                    '<div class="col-12 text-center">',
                        '<h1><i class="fas fa-print" aria-hidden="true"></i></h1>',
                        '<h4><span>' + response.message + '</span></h4>',
                        '<h4><span>' + response.document + '</span></h4>',
                    '</div>',
                '</div>',
                '<div class="d-none">',
                    '<img src="http://localhost:8089?documento=' + response.code + '" alt="remote-printer"/>',
                '</div>'
            ].join("\n");

            dialog.find('.bootbox-body').html(html);

            setTimeout(function(){
                dialog.modal('hide');
            }, 400);
        }
    });
}