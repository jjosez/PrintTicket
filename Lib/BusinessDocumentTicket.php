<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Customer;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Document;
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
    public function __construct($document, $doctype = "general", int $width = null, DocumentTemplate $template = null)
    {
        $this->document = $document;
        $this->doctype = $doctype;
        $width = $width ?: $this->getDefaultWitdh();

        $this->template = $template ?: new DefaultDocumentTemplate($width);
    }

    public function getTicket()
    {
        $xcompany = $this->document->getCompany();
        $company = new Company(
            $xcompany->nombrecorto,
            $xcompany->cifnif,
            $xcompany->direccion
        );

        $document = new Document(
            $this->document->codigo,
            $this->document->total,
            $this->document->totaliva,
            null
        );

        foreach ($this->document->getLines() as $line) {
            $code = $line->referencia ? $line->referencia : '';
            $document->addLine(
                $code,
                $line->descripcion, 
                $line->pvpunitario, 
                $line->cantidad, 
                $line->iva
            );
        }

        $customer = new Customer(
            $this->document->nombrecliente,
            $this->document->cifnif,
            $this->document->direccion,
            ''
        );

        $headlines = $this->getCustomLines('header');
        $footlines = $this->getCustomLines('footer');

        return $this->template->buildTicket($document, $customer, $company, $headlines, $footlines);
    }

    private function getCustomLines(string $position)
    {
        $data = (new TicketCustomLine)->getFromDocument($this->doctype, $position);
        $result = [];

        foreach ($data as $line) {
            $lines[] = $line->texto;
        }

        return $result;
    }

    private function getDefaultWitdh()
    {
        return AppSettings::get('ticket', 'linelength', 50);
    }
}
