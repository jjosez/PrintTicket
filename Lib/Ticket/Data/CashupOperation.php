<?php 

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Data;

/**
 *          
 */
class CashupOperation
{
    private $id;
    private $code;
    private $amount;

    function __construct(string $id, string $code, $amount)
    {
        $this->id = $id;
        $this->code = $code;
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getId()
    {
        return $this->id;
    }
}
