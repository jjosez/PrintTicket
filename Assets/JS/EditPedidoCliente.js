const query = window.location.search;
const params = new URLSearchParams(query);
const code = params.get('code');

function printTicketDialog() {
    (async() => {
        let printModule = await import('./PrintTicket.js');
        printModule.print('PedidoCliente', code);
    })();
}

$(function () {
    if (!code) {
        $('#printTicketBtn').attr('disabled', true);
    }
});