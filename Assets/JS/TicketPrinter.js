const query = window.location.search;
const params = new URLSearchParams(query);
const code = params.get('code');

export function showPrinterDialog(documentName) {
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
                    setPrintRequest({documentCode: code, documentName: documentName});
                }
            },
            normal: {
                label: '<i class="fas fa-print"></i> Normal',
                className: 'btn-primary',
                callback: function () {
                    setPrintRequest({documentCode: code, documentName: documentName}, 1);
                }
            }
        }
    });
}

function setPrintRequest({documentCode, documentName}, asGift = 0) {
    const data = new FormData();

    data.append('codigo', documentCode);
    data.append('tipo', documentName);
    data.append('regalo', asGift);

    fetch('PrintTicket', {method: 'POST', body: data})
        .then(response => response.json())
        .then(data => {
            return requestPrinterService(data);
        })
        .catch(error => showPrintMessage('No se pudo conectar al servicio de impresion: ' + error));
}

function showPrintMessage(message) {
    bootbox.alert({title: 'Error', message: message});
}

async function requestPrinterService({code}) {
    let params = new URLSearchParams({"documento": code});

    await fetch('http://localhost:8089?' + params, {
        mode: 'no-cors',
        method: 'GET'
    });
}
