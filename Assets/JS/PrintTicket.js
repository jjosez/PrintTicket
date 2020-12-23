export function print(document, code) {
    let data = {
        code: code,
        documento: document
    };
    $.ajax({
        type: 'POST',
        url: 'PrintTicket',
        data: data,
        success: function (message) {
            bootbox.dialog({
                message: message,
                onEscape: true,
                backdrop: true,
            })
        }
    });
}