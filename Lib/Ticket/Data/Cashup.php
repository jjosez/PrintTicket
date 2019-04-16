<?php 

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Data;

/**
 *          
 */
class Cashup
{
    private $code;    
    private $spectedTotal;
    private $total;
    private $date;
    private $payments = [];


    function __construct(string $code, string $spectedTotal, string $total, ?DateTime $date)
    {
        $this->code = $code;
        $this->spectedTotal = $spectedTotal;
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

    public function getSpectedTotal() : string
    {
        return $this->total;
    }

    public function getTotal() : string
    {
        return $this->totalTax;
    }

    public function getPayments() : array
    {
        return $this->payments;
    }

    public function addPayment(string $method, $amount) : DocumentPayment
    {
        $this->payments[] = new DocumentPayment($method, $amount);
    }
}
