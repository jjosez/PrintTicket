<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use DateTime;
use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Lib\Ticket\Data;
use FacturaScripts\Dinamic\Lib\Ticket\TicketBuilder;
use FacturaScripts\Dinamic\Lib\Ticket\Template; 
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
        $company = new Data\Company(
            $xcompany->nombrecorto,
            $xcompany->cifnif,
            $xcompany->direccion
        );

        $document = new Data\Document(
            $this->document->codigo,
            $this->document->total,
            $this->document->totaliva,
            null
        );

        $customer = new Data\Customer(
            $this->document->nombrecliente,
            $this->document->cifnif,
            $this->document->direccion,
            null
        );

        $data = (new TicketCustomLine)->getFromDocument('general', 'header');
        $headlines = $this->getLines($data);
        
        $data = (new TicketCustomLine)->getFromDocument('general', 'footer');
        $footlines = $this->getLines($data);

        $width = AppSettings::get('ticket', 'linelength');
        $template = new Template\DefaultTemplate($width);

        $builder = new TicketBuilder($company,'80',null);
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
