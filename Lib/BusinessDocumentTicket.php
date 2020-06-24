<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use DateTime;
use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Customer;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Document;
use FacturaScripts\Dinamic\Model\TicketCustomLine;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template\DefaultDocumentTemplate;

class BusinessDocumentTicket
{
    private $document;
    private $doctype;
    private $width;

    /**
     * BusinessDocumentTicket constructor.
     *
     * @param $document
     * @param string $doctype identificador del tipo de documento
     * @param int|null $width numero maximo de caracteres por linea.
     */
    function __construct($document, $doctype = "general", int $width = null)
    {
        $this->document = $document;
        $this->doctype = $doctype;
        $this->width = (empty($width)) ? $this->getDefaultWitdh() : $width;
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
            null
        );

        $data = (new TicketCustomLine)->getFromDocument($this->doctype, 'header');
        $headlines = $this->getCustomLines($data);
        
        $data = (new TicketCustomLine)->getFromDocument($this->doctype, 'footer');
        $footlines = $this->getCustomLines($data);

        $template = new DefaultDocumentTemplate($this->width);

        $builder = new Ticket\TicketBuilder($company, $template);
        return $builder->buildFromDocument($document, $customer, $headlines, $footlines);
    }

    private function getCustomLines($data)
    {
        $lines = [];
        foreach ($data as $line) {
            $lines[] = $line->texto;
        }

        return $lines;
    }

    private function getDefaultWitdh()
    {
        return AppSettings::get('ticket', 'linelength', 50);
    }
}
