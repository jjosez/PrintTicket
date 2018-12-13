<?php 
namespace FacturaScripts\Plugins\PrintTicket\Lib\TicketBuilder;

use FacturaScripts\Dinamic\Lib\TicketBuilder;

/**
* Clase para imprimir tickets de facturas.
* Si requieres personalizar tu ticket es esta clase la que necesitas modificar.
*/
class TicketBuilderPedido extends TicketBuilder\TicketBuilder
{
    public function __construct($width = null, $comands = FALSE) 
    {
        parent::__construct($width, $comands);
    }
}