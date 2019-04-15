<?php 

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Data;

use DateTime;

/**
 *          
 */
class Document
{
    private $code;    
    private $total;
    private $totalTax;
    private $date;
    private $lines = [];
    private $payments = [];


    function __construct(string $code, string $total, string $totalTax, ?DateTime $date)
    {
        $this->code = $code;
        $this->total = $total;
        $this->totalTax = $totalTax;
        $this->date = $date ?: new DateTime();
    }

    public function getCode() : string
    {
        return $this->code;
    }

    public function getDate() : string
    {
        return $this->date;
    }

    public function getTotal() : string
    {
        return $this->total;
    }

    public function getTotalTax() : string
    {
        return $this->totalTax;
    }

    public function getLines() : array
    {
        return $this->lines;
    }

    public function getPayments() : array
    {
        return $this->payments;
    }

    public function addLine(string $code, $price, $quantity, $tax) : DocumentLine
    {
        $this->lines[] = new DocumentLine($code, $price, $quantity, $tax);
    }

    public function addPayment(string $method, $amount) : DocumentPayment
    {
        $this->payments[] = new DocumentPayment($method, $amount);
    }
}
