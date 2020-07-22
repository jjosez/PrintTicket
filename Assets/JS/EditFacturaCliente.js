const query = window.location.search;
const params = new URLSearchParams(query);
const code = params.get('code');

function printTicketDialog() {
    var data = {
        code: code,
        documento: 'FacturaCliente'
    };
    $.ajax({
        type: 'POST',
        url: 'PrintTicket',
        data: data,
        success: function(message) {
            bootbox.dialog({
                message: message,
                onEscape: true,
                backdrop: true,
            })
        }
    });
}
$(function() {
    if (!code) {
        $('#printTicketBtn').attr('disabled', true);
    }
});