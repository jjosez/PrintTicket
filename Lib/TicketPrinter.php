<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Lib\TicketBuilder;
use FacturaScripts\Dinamic\Model\Ticket;
use FacturaScripts\Dinamic\Model\TicketCustomLine;

class TicketPrinter
{
    private $errors = [];

    public function printTicket($businessDocument)
    {
        $width = AppSettings::get('ticket', 'linelength');
        $gift = AppSettings::get('ticket', 'gift', false);

        $businessDocumentClass = $businessDocument->modelClassName();
        $className = 'FacturaScripts\\Dinamic\\Lib\\TicketBuilder\\TicketTemplate' . $businessDocumentClass;

        if (!class_exists($className)) {
            $this->errors[] = 'No se encontro la plantilla para ' . $businessDocumentClass;
            return false;
        } 

        $template = new $className($businessDocument, $width, $gift);   

        if (!isset($template)) {
            $this->errors[] = 'Error al cargar la plantilla ' .$businessDocumentClass;
            return false;
        }

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

        $this->errors[] = 'Error al guardar el ticket ' .$businessDocumentClass;
        return false;        
    }

    public function getErrors()
    {
        return $this->errors;
    }
}