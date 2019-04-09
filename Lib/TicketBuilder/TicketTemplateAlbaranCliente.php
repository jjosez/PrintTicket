<?php 
namespace FacturaScripts\Plugins\PrintTicket\Lib\TicketBuilder;

use FacturaScripts\Dinamic\Lib\TicketBuilder;

/**
* Clase para imprimir tickets de facturas.
* Si requieres personalizar tu ticket es esta clase la que necesitas modificar.
*/
class TicketTemplateAlbaranCliente extends TicketBuilder\TicketTemplateMaster
{
    function __construct($document, $width = 45) 
    {
        parent::__construct($document, $width);
        
        $this->documentTitle = $this->i18n->trans('customer-delivery-note');
    }
    
}