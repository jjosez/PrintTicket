<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Core\Model\Base\BusinessDocument;
use FacturaScripts\Dinamic\Lib\Ticket\Template\DocumentTemplate;
use FacturaScripts\Dinamic\Lib\Ticket\Template\DefaultDocumentTemplate;
use FacturaScripts\Dinamic\Model\TicketCustomLine;

class BusinessDocumentTicket
{
    private $document;
    private $doctype;
    private $template;

    /**
     * BusinessDocumentTicket constructor.
     *
     * @param $document
     * @param DocumentTemplate|null $template
     * @param string $doctype identificador del tipo de documento
     * @param int|null $width numero maximo de caracteres por linea.
     */
    public function __construct(BusinessDocument $document, $doctype = "general", int $width = null, DocumentTemplate $template = null)
    {
        $this->document = $document;
        $this->doctype = $doctype;
        $width = $width ?: $this->getDefaultWitdh();

        $this->template = $template ?: new DefaultDocumentTemplate($width);
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

    private function getDefaultWitdh()
    {
        return AppSettings::get('ticket', 'linelength', 50);
    }
}
