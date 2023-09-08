<?php
namespace FacturaScripts\Plugins\PrintTicket\Model;

use FacturaScripts\Core\Model\Base;

class Ticket extends Base\ModelClass
{
    use Base\ModelTrait;

    public $abrircajon;
    public $coddocument;
    public $cortarpapel;
    public $name;

    public $text;

    public function clear()
    {
        parent::clear();
        $this->abrircajon = true;
        $this->cortarpapel = true;
    }

    public function install()
    {
        new TicketCustomLine();
        return parent::install();
    }

    public static function primaryColumn(): string
    {
        return 'coddocument';
    }

    public static function tableName(): string
    {
        return 'tickets';
    }
}
