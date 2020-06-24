function printTicketDialogGeneral() {
    const url = window.location.search;
    const params = new URLSearchParams(url);
    const path = window.location.pathname.split('/')[2];

    var data = {
        code: params.get('code'),
        documento: path.substring(4)
    }

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
    })
}