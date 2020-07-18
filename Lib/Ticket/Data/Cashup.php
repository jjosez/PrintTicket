<?php 

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Data;

use DateTime;

/**
 *          
 */
class Cashup
{
    private $code;
    private $date;
    private $initial;
    private $operations = [];
    private $payments = [];
    private $spected;
    private $total;


    function __construct(string $code, string $initial, string $spected, string $total, DateTime $date = null)
    {
        $this->code = $code;
        $this->initial = $initial;
        $this->spected = $spected;
        $this->total = $total;
        $this->date = $date ?: new DateTime();
    }

    public function getCode() : string
    {
        return $this->code;
    }

    public function getDate() : string
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    public function getInitialAmount() : string
    {
        return $this->initial;
    }

    public function getSpectedTotal() : string
    {
        return $this->spected;
    }

    public function getTotal() : string
    {
        return $this->total;
    }

    public function getOperations() : array
    {
        return $this->operations;
    }

    public function getPayments() : array
    {
        return $this->payments;
    }

    public function addOperation(string $id, string $code, $amount) : CashupOperation
    {
        return $this->operations[] = new CashupOperation($id, $code, $amount);
    }

    public function addPayment(string $method, $amount)
    {
        $this->payments[] = new DocumentPayment($method, $amount);
    }
}
