<?php 

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Data;

/**
 *          
 */
class DocumentLine
{
    protected $code;
    protected $price;
    protected $quantity;
    protected $tax;

    function __construct(string $code, $price, $quantity, $tax = null)
    {
        $this->code = $code;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->tax = $tax;
    }

    public function getCode() : string
    {
        return $this->code;
    }

    public function getPrice()
    {
        return $this->price;
    } 

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getTax()
    {
        return $this->tax;
    }

    
}
