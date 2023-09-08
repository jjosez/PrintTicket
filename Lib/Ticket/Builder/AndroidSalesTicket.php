<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Builder;

class AndroidSalesTicket extends SalesTicket
{
    public function getResult(): string
    {
        $buffer = parent::getResult();

        return "intent:base64," . base64_encode($buffer) . "#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;";
    }
}
