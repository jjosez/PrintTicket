<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib\TicketBuilder;

class TicketBuilder
{
    use TicketBuilderTrait;

    public function __construct($width = null, $printprice = TRUE, $comands = FALSE) 
    {
        $this->ticket = '';

        $this->paperWidth = ($width) ? $width : '45';        
        $this->commandToCut = '27.105';
        $this->commandToOpen = '27.112.48';
        $this->disabledCommands = $comands;
        $this->printPrice = $printprice;
    }
}