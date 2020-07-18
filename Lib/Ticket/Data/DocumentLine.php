<?php 

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Data;

/**
 *          
 */
class DocumentLine
{
    protected $code;
    protected $description;
    protected $price;
    protected $quantity;
    protected $tax;

    function __construct(string $code, $description, $price, $quantity, $tax = null)
    {
        $this->code = $code;
        $this->description = $description;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->tax = $tax;
    }

    public function getCode() : string
    {
        return $this->code;
    }

    public function getDescription() : string
    {
        return $this->description;
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

    public function getTotal()
    {
        return $this->price * $this->quantity;
    }
}
