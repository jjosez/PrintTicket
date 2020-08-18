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

    public function clear()
    {
        parent::clear();
        $this->abrircajon = false;
        $this->cortarpapel = false;
    }

    public function install()
    {
        new TicketCustomLine();
        return parent::install();
    }

    public static function primaryColumn()
    {
        return 'coddocument';
    }

    public static function tableName()
    {
        return 'tickets';
    }
}