<?php
namespace FacturaScripts\Plugins\PrintTicket\Model;

use FacturaScripts\Core\Model\Base;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;

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

    public function getFromDocument($document, $position)
    {
        $where = [
          new DataBaseWhere('documento', $document),
          new DataBaseWhere('posicion', $position, '='),
        ];

        //$sqlWhere = DataBase\DataBaseWhere::getSQLWhere($where);

        return $this->all($where);
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