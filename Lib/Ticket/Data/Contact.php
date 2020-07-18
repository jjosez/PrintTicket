<?php 

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Data;

/**
 * 
 */
class Contact
{    
    protected $name;
    protected $vatID;
    protected $address;
    protected $phone;    

    function __construct(string $name, string $vatID, string $address = '', string $phone = '')
    {
        $this->name = $name;
        $this->vatID = $vatID;
        $this->address = $address;
        $this->phone = $phone;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getVatID() : string
    {
        return $this->vatID;
    }

    public function getAddress() : string
    {
        return $this->address;
    }

    public function getPhone() : string
    {
        return $this->phone;
    }
}
