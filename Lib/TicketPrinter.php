<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Lib\TicketBuilder;
use FacturaScripts\Dinamic\Model\Ticket;
use FacturaScripts\Dinamic\Model\TicketCustomLine;

class TicketPrinter
{
    private $errors = [];
    private $template;

    public function printTicket($businessDocument, $asGift = false, $cutPapper = true, $openDrawer = true)
    {
        if (!$this->initTemplate($businessDocument)) {
            return false;
        }

        $width = AppSettings::get('ticket', 'linelength');
        $footertext = AppSettings::get('ticket', 'footertext');
        $headerLines = (new TicketCustomLine)->getFromDocument('general', 'header');
        $footerLines = (new TicketCustomLine)->getFromDocument('general', 'footer');

        $this->template->setCustomHeaderLines($headerLines); 
        $this->template->setCustomFooterLines($footerLines);      
        $this->template->setFooterText($footertext);

        $ticket = new Ticket();
        $ticket->coddocument = $businessDocument->modelClassName();
        $ticket->text = $this->template->toString($asGift, $cutPapper, $openDrawer);     

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

    private function initTemplate($businessDocument)
    {
        $businessDocumentClass = $businessDocument->modelClassName();
        $templateClassName = 'FacturaScripts\\Dinamic\\Lib\\TicketBuilder\\TicketTemplate' . $businessDocumentClass;

        if (!class_exists($templateClassName)) {
            $this->errors[] = 'No se encontro la clase TicketTemplate' . $businessDocumentClass;
            return false;
        } 

        $this->template = new $templateClassName($businessDocument, $width);   

        if (!isset($this->template)) {
            $this->errors[] = 'Error al cargar la clase TicketTemplate' . $businessDocumentClass;
            return false;
        }

        return true;
    }
}