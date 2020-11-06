<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Data;

/**
 *
 */
class DocumentPayment
{
    private $method;
    private $amount;

    function __construct(string $method, $amount)
    {
        $this->method = $method;
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getMethod()
    {
        return $this->method;
    }
}