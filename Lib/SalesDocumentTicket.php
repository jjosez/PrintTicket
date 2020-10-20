<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use FacturaScripts\Core\Model\Base\BusinessDocument;
use FacturaScripts\Dinamic\Lib\Ticket\Template\SalesTemplate;
use FacturaScripts\Dinamic\Model\TicketCustomLine;

class SalesDocumentTicket
{
    private $document;
    private $doctype;
    private $template;

    /**
     * SalesDocumentTicket constructor.
     *
     * @param $document
     * @param SalesTemplate|null $template
     * @param string $doctype identificador del tipo de documento
     * @param int|null $width numero maximo de caracteres por linea.
     */
    public function __construct(BusinessDocument $document, $doctype = "general", $width = null, SalesTemplate $template = null)
    {
        $this->document = $document;
        $this->doctype = $doctype;

        $this->template = $template ?: new SalesTemplate($document->getCompany(), $width);
    }

    public function getTicket()
    {
        $headlines = $this->getCustomLines('header');
        $footlines = $this->getCustomLines('footer');

        return $this->template->buildTicket($this->document, $headlines, $footlines);
    }

    private function getCustomLines(string $position)
    {
        return TicketCustomLine::rawFromDocument($this->doctype, $position);
    }
}
