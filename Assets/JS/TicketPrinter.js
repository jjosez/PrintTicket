const query = window.location.search;
const params = new URLSearchParams(query);
const code = params.get('code');

export function showPrinterDialog(documentName) {
    if (code === undefined || code == null) {
        return;
    }

    const data = new FormData();
    data.append('action', 'get-ticket-formats')
    data.append('tipo-documento', documentName);

    fetch('PrintTicket', {method: 'POST', body: data})
        .then(response => response.json())
        .then(data => {
            for (const item of data) {
                item.label = item.nombre;
                item.className = 'btn-primary btn-block';
                item.callback = function () {
                    printRequest({
                        documentCode: code, documentFormat: item.id, documentName: documentName
                    });
                }
            }

            bootbox.dialog({
                message: '<h4>¿Qué típo de impresión deseas?</h4>', size: 'medium', buttons: data
            });
        })
        .catch(error => showPrintMessage('Error al obtener los formatos de impresion'));
}

async function printRequest({documentCode, documentFormat, documentName}) {
    const data = new FormData();

    data.append('codigo', documentCode);
    data.append('formato', documentFormat);
    data.append('tipo', documentName);

    await printOnDesktop(data);
}

async function printOnDesktop(data) {
    data.set('action', 'print-desktop-ticket');

    return corePrintRequest(data)
        .then(response => response.json())
        .then(data => printerServiceRequest(data))
        .catch(error => showPrintMessage('No se pudo conectar al servicio de impresion: ' + error));
}

async function printerServiceRequest({print_job_id}) {
    let params = new URLSearchParams({"documento": print_job_id});

    await fetch('http://127.0.0.1:8089?' + params, {
        mode: 'no-cors', method: 'GET'
    });
}

function showPrintMessage(message) {
    bootbox.alert({title: 'Error', message: message});
}

function corePrintRequest(data) {
    const init = {method: 'POST', body: data};
    const controllerAddress = 'PrintTicket';

    return fetch(controllerAddress, init);
}
