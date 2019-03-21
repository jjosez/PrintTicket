<?php 
namespace FacturaScripts\Plugins\PrintTicket\Lib\TicketBuilder;

use FacturaScripts\Dinamic\Lib\TicketBuilder;

/**
* Clase para imprimir tickets de facturas.
* Si requieres personalizar tu ticket es esta clase la que necesitas modificar.
*/
class TicketTemplateFacturaCliente extends TicketBuilder\TicketTemplateMaster
{
    function __construct($document, $width = 45, $gift = false, $opendrawer = true, $cutpaper = true) 
    {
        parent::__construct($document, $width, $gift, $opendrawer, $cutpaper);
        
        $this->documentTitle = $this->i18n->trans('customer-invoice');
    }    
}