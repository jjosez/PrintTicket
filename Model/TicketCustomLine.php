<?php
namespace FacturaScripts\Plugins\PrintTicket\Model;

use FacturaScripts\Core\Model\Base;

class TicketCustomLine extends Base\ModelClass
{
    use Base\ModelTrait;

    public $documento;
    public $idlinea;
    public $posicion;
    public $texto;

    public function cleanFromDocument($document, $position)
    {
        $lines = array();

        $sql = 'DELETE FROM ' . static::tableName() 
            . ' WHERE documento = ' . self::$dataBase->var2str($document) 
            . ' AND posicion = ' . self::$dataBase->var2str($position) . ';';

        if (self::$dataBase->exec($sql)) {
            return true;
        }

        return false;
    }

    public static function primaryColumn()
    {
        return 'idlinea';
    }

    public static function tableName()
    {
        return 'ticketcustomlines';
    }
}