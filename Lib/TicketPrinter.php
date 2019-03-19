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
        $gift = AppSettings::get('ticket', 'gift', false);

        $businessDocumentClass = $businessDocument->modelClassName();
        $className = 'FacturaScripts\\Dinamic\\Lib\\TicketBuilder\\TicketTemplate' . $businessDocumentClass;

        if (class_exists($className)) {
            $template = new $className($businessDocument, $width, $gift);
        }         

        if (isset($template)) {
            $footertext = AppSettings::get('ticket', 'footertext');
            $headerLines = (new TicketCustomLine)->getFromDocument('general', 'header');
            $footerLines = (new TicketCustomLine)->getFromDocument('general', 'footer');

            $template->setCustomHeaderLines($headerLines); 
            $template->setCustomFooterLines($footerLines);      
            $template->setFooterText($footertext);

            $ticket = new Ticket();
            $ticket->coddocument = $businessDocumentClass;
            $ticket->text = $template->toString();
            
            if ($ticket->save()) {
                return true;
            }            
        }

        return false;        
    }
}