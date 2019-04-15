<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\ESCPOS\Printer;
use FacturaScripts\Dinamic\Lib\Ticket\Data;

/**
 * 
 */
abstract class TicketTemplate
{
    protected $company;
    protected $printer;

    function __construct($width = '45')
    {
        $this->printer = new Printer($width);
    }

    public abstract function buildDocumentTicket(
        Data\Document $document, 
        Data\Customer $customer, 
        Data\Company $company, 
        array $headlines, 
        array $footlines
    ) : string;

    public abstract function buildCashupTicket(Cashup $cashup, Company $company) : string;
}
