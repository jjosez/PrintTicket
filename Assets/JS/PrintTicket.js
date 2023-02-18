const query = window.location.search;
const params = new URLSearchParams(query);
const code = params.get('code');

export function showTicketDialog(documentName) {
    if (code === undefined || code == null) {
        return;
    }

    bootbox.dialog({
        message: '<h4>¿Qué típo de impresión deseas?</h4>',
        size: 'medium',
        buttons: {
            gift: {
                label: '<i class="fas fa-gift"></i> Regalo',
                className: 'btn-warning',
                callback: function () {
                    //getPrintJob({dialog: this, code: code, document: document, gift: 1});
                    return false;
                }
            },
            normal: {
                label: '<i class="fas fa-print"></i> Normal',
                className: 'btn-primary',
                callback: function () {
                    //getPrintJob({dialog: this, code: code, document: document, gift: 0});
                    return false;
                }
            }
        }
    });
}

export function print(document) {
    if (code === undefined || code === null) {
        return;
    }

    bootbox.dialog({
        message: '<h4>¿Qué típo de impresión deseas?</h4>',
        size: 'medium',
        buttons: {
            gift: {
                label: '<i class="fas fa-gift"></i> Regalo',
                className: 'btn-warning',
                callback: function () {
                    getPrintJob({dialog: this, code: code, document: document, gift: 1});
                    return false;
                }
            },
            normal: {
                label: '<i class="fas fa-print"></i> Normal',
                className: 'btn-primary',
                callback: function () {
                    getPrintJob({dialog: this, code: code, document: document, gift: 0});
                    return false;
                }
            }
        }
    });
}

function getPrintJob({dialog, code, document, gift = 0}) {
    const data = new FormData();

    data.set('code', code);
    data.set('documento', document);
    data.set('gift', gift);

    const options = {method: 'POST', body: data};

    fetch('PrintTicket', options)
        .then(response => response.json())
        .then(data => {
            setTimeout(function () {
                dialog.modal('hide');
            }, 200);

            return sendPrintJob(data);
        })
        .catch(error => console.log('No se pudo conectar al servicio de impresion', error.message));
}

async function sendPrintJob({code}) {
    let params = new URLSearchParams({"documento": code});

    await fetch('http://localhost:8089?' + params, {mode: 'no-cors', method: 'GET'})
        .catch(error => console.log('Error printing.', error.message));
}
