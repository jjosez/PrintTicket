<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket;

use FacturaScripts\Dinamic\Lib\Ticket\Template; 
use FacturaScripts\Dinamic\Lib\Ticket\Data; 

/**
 * 
 */
class TicketBuilder
{
    private $company;
    private $template;
    
    function __construct(Data\Company $company, $width, ?Template\TicketTemplate $template)
    {
        $this->company = $company;
        $this->template = $template ?: new Template\DefaultTemplate($width);
    }

    public function buildFromDocument(Data\Document $document, Data\Customer $customer, array $headlines, array $footlines)
    {
        return $this->template->buildDocumentTicket($document, $customer, $this->company, $headlines, $footlines);
    }

    public function buildFromCashup(Data\Cashup $cashup)
    {
        return $this->template->buildCashupTicket($cashup, $this->company);
    }
}
