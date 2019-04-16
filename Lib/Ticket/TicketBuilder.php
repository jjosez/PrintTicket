<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket;

use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Customer;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Document;
use FacturaScripts\Dinamic\Lib\Ticket\Template\DefaultTemplate;  

/**
 * 
 */
class TicketBuilder
{
    private $company;
    private $template;
    
    function __construct(Company $company, ?DefaultTemplate $template)
    {
        $this->company = $company;
        $this->template = ($template) ?: new DefaultTemplate();
    }

    public function buildFromDocument(
        Document $document, 
        Customer $customer, 
        array $headlines, 
        array $footlines,
        bool $cut = true,
        bool $open = true
    ) {
        return $this->template->buildDocumentTicket(
            $document, 
            $customer, 
            $this->company, 
            $headlines, 
            $footlines, 
            $cut, 
            $open
        );
    }

    public function buildFromCashup(
        Cashup $cashup, 
        bool $cut = true, 
        bool $open = true
    ) {
        return $this->template->buildCashupTicket(
            $cashup, 
            $this->company,
            $cut,
            $open
        );
    }
}
