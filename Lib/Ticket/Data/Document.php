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


    function __construct(string $code, string $total, string $totalTax, DateTime $date = null)
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
        return $this->date->format('Y-m-d H:i:s');
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

    public function addLine(string $code, $description, $price, $quantity, $tax) : DocumentLine
    {
        return $this->lines[] = new DocumentLine($code, $description, $price, $quantity, $tax);
    }

    public function addPayment(string $method, $amount) : DocumentPayment
    {
        return $this->payments[] = new DocumentPayment($method, $amount);
    }
}
