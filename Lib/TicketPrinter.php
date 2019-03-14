<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Lib\TicketBuilder;
use FacturaScripts\Dinamic\Model\Ticket;
use FacturaScripts\Dinamic\Model\TicketCustomLine;

class TicketPrinter
{
    public function printTicket($businessDocument)
    {
        $width = AppSettings::get('ticket', 'linelength');
        $price = AppSettings::get('ticket', 'printprice');

        $businessDocumentClass = $businessDocument->modelClassName();
        switch ($businessDocumentClass) {
            case 'AlbaranCliente':
                $builder = new TicketBuilder\TicketBuilderAlbaran($width, !$price); 
                break;

            case 'FacturaCliente':
                $builder = new TicketBuilder\TicketBuilderFactura($width, $price);
                break;
            
            case 'PedidoCliente':
                $builder = new TicketBuilder\TicketBuilderPedido($width, $price);
                break;
            
            default:
                # code...
                break;
        }

        if (isset($builder)) {
            $footertext = AppSettings::get('ticket', 'footertext');
            $headerLines = (new TicketCustomLine)->getFromDocument('general', 'header');
            $footerLines = (new TicketCustomLine)->getFromDocument('general', 'footer');

            $builder->setCompany($businessDocument->getCompany());
            $builder->setDocument($businessDocument);
            $builder->setCustomHeaderLines($headerLines); 
            $builder->setCustomFooterLines($footerLines);      
            $builder->setFooterText($footertext);

            $ticket = new Ticket();
            $ticket->coddocument = $businessDocumentClass;
            $ticket->text = $builder->toString();
            
            if ($ticket->save()) {
                return true;
            }            
        }

        return false;        
    }
}