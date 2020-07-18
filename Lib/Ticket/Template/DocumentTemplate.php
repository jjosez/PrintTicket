<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Customer;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Document;

/**
 *
 */
abstract class DocumentTemplate extends BaseTicketTemplate
{
    protected $customer;
    protected $document;
    protected $headLines;
    protected $footLines;

    public function __construct($width = '50')
    {
        parent::__construct($width);

        $this->headLines = [];
        $this->footLines = [];
    }

    abstract public function buildTicket(
        Document $document,
        Customer $customer,
        Company $company,
        array $headlines,
        array $footlines,
        bool $cut = true,
        bool $open = true
    ): string;
}
