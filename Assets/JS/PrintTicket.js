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
                label: "Normal",
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
        data: data,
        success: function (message) {
            dialog.find('.bootbox-body').html(message);
        }
    });
}