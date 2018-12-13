<?php
namespace FacturaScripts\Plugins\PrintTicket\Model;

use FacturaScripts\Core\Model\Base;

class Ticket extends Base\ModelClass
{
    use Base\ModelTrait;

    public $coddocument;
    public $name;

    public static function primaryColumn()
    {
        return 'coddocument';
    }

    public static function tableName()
    {
        return 'tickets';
    }
}