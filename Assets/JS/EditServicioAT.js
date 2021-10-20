async function printTicketDialog() {
  const { print } = await import('./PrintTicket.js');
  print('Servicio');
}
