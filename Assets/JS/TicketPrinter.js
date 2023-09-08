const query = window.location.search;
const params = new URLSearchParams(query);
const code = params.get('code');

export function showPrinterDialog(documentName) {
    if (code === undefined || code == null) {
        return;
    }

    const data = new FormData();
    data.append('action', 'get-formats')
    data.append('tipo-documento', documentName);

    fetch('PrintTicket', {method: 'POST', body: data})
        .then(response => response.json())
        .then(data => {
            for (const item of data) {
                item.label = item.nombre;
                item.className = 'btn-primary btn-block';
                item.callback = function () {
                    printRequest({
                        documentCode: code,
                        documentFormat: item.id,
                        documentName: documentName
                    });
                }
            }

            bootbox.dialog({
                message: '<h4>¿Qué típo de impresión deseas?</h4>',
                size: 'medium',
                buttons: data
            });
        })
        .catch(error => showPrintMessage('Error al obtener los formatos de impresion: ' + error));
}

function printRequest({documentCode, documentFormat, documentName}) {
    const data = new FormData();

    data.append('action', 'print-document');
    data.append('codigo', documentCode);
    data.append('formato', documentFormat);
    data.append('tipo', documentName);

    fetch('PrintTicket', {method: 'POST', body: data})
        .then(response => response.json())
        .then(data => {
            return printerServiceRequest(data);
        })
        .catch(error => showPrintMessage('No se pudo conectar al servicio de impresion: '  + error));
}

function showPrintMessage(message) {
    bootbox.alert({title: 'Error', message: message});
}

async function printerServiceRequest({code}) {
    let params = new URLSearchParams({"documento": code});

    await fetch('http://localhost:8089?' + params, {
        mode: 'no-cors',
        method: 'GET'
    });
}
