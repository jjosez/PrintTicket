const query = window.location.search;
const params = new URLSearchParams(query);
const code = params.get('code');

const message = [
    '<div class="row">',
    '<div class="col-12 text-center">',
    '<h1><i class="fas fa-print" aria-hidden="true"></i></h1>',
    '</div>',
    '</div>'
].join("\n");

export function print(document) {
    bootbox.dialog({
        message: '<h4>¿Qué típo de impresión deseas?</h4>',
        size: 'medium',
        buttons: {
            gift: {
                label: '<i class="fas fa-gift"></i> Regalo',
                className: 'btn-warning',
                callback: function () {
                    getPrintJob(this, code, document, 1);
                    return false;
                }
            },
            normal: {
                label: '<i class="fas fa-print"></i> Normal',
                className: 'btn-primary',
                callback: function () {
                    getPrintJob(this, code, document, 0);
                    return false;
                }
            }
        }
    });
}

function getPrintJob(dialog, code, document, gift = 0) {
    const data = new FormData();

    data.set('code', code);
    data.set('documento', document);
    data.set('gift', gift);

    const options = { method: 'POST', body: data };

    fetch('PrintTicket', options)
        .then(response => response.json())
        .then(data => {
            setTimeout(function () {
                dialog.modal('hide');
            }, 200);

            return fetch('http://localhost:8089?documento=' + data.code);
        })
        .catch(err => console.log('No se pudo conectar al servicio de impresion'));
}