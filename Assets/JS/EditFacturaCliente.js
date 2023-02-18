async function ticketPrinterAction() {
    const {showPrinterDialog} = await import('./TicketPrinter.js');

    showPrinterDialog('FacturaCliente');
}
