<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Model\Empresa;

/**
 * 
 */
class CashupTemplate extends BaseTicketTemplate
{
    protected $cashup;

    public function __construct(Empresa $empresa, int $width)
    {
        parent::__construct($empresa, $width);
    }

    protected function buildTicket(Cashup $cashup, bool $cut, bool $open) : string
    {

    }
}
