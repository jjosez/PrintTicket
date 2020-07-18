<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Lib\Ticket\ESCPOS\Printer;

/**
 *
 */
abstract class BaseTicketTemplate
{
    protected $company;
    protected $printer;

    public function __construct($width)
    {
        $this->printer = new Printer($width);
    }

    protected function cutPapperCommand(bool $cut)
    {
        if (true === $cut) {
            $this->printer->cut();
            return;
        }
    }

    protected function openDrawerCommand(bool $open)
    {
        if (true === $open) {
            $this->printer->open();
            return;
        }
    }
}
