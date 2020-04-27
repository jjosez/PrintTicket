<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Lib\Ticket\ESCPOS\Printer;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Customer;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Document;

/**
 * 
 */
abstract class AbstractTemplate
{
    protected $company;
    protected $printer;

    public function __construct($width = '50')
    {
        $this->printer = new Printer($width);
    }

    abstract public function buildDocumentTicket(
        Document $document, 
        Customer $customer, 
        Company $company, 
        array $headlines, 
        array $footlines,
        bool $cut,
        bool $open
    ) : string;

    abstract public function buildCashupTicket(
        Cashup $cashup, 
        Company $company,
        bool $cut,
        bool $open
    ) : string;
}
