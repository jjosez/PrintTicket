<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;

/**
 * 
 */
abstract class CashupTemplate extends BaseTicketTemplate
{
    protected $cashup;
    protected $company;

    public function __construct($width)
    {
        parent::__construct($width);
    }

    abstract public function buildTicket(
        Cashup $cashup,
        Company $company,
        bool $cut,
        bool $open
    ) : string;
}
