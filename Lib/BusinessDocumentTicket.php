<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use DateTime;
use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Customer;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Document;
use FacturaScripts\Dinamic\Lib\Ticket\Template\DefaultTemplate;
use FacturaScripts\Dinamic\Model\TicketCustomLine;

class BusinessDocumentTicket
{
    private $document;

    function __construct($document)
    {
        $this->document = $document;
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
            $document->addLine($line->referencia, $line->descripcion, $line->pvpunitario, $line->cantidad, $line->iva);
        }

        $customer = new Customer(
            $this->document->nombrecliente,
            $this->document->cifnif,
            $this->document->direccion,
            null
        );

        $data = (new TicketCustomLine)->getFromDocument('general', 'header');
        $headlines = $this->getLines($data);
        
        $data = (new TicketCustomLine)->getFromDocument('general', 'footer');
        $footlines = $this->getLines($data);

        $width = AppSettings::get('ticket', 'linelength', 50);
        $template = new DefaultTemplate($width);

        $builder = new Ticket\TicketBuilder($company, $template);
        return $builder->buildFromDocument($document, $customer, $headlines, $footlines);
    }

    private function getLines($data)
    {
        $lines = [];
        foreach ($data as $line) {
            $lines[] = $line->texto;
        }

        return $lines;
    }
}
