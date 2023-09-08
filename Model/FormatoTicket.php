<?php

namespace FacturaScripts\Plugins\PrintTicket\Model;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Model\Base;

class FormatoTicket extends Base\ModelClass
{
    use Base\ModelTrait;

    public const DEFAULT_BODY_FONTSIZE = 1;
    public const DEFAULT_TITLE_FONTSIZE = 1;
    public const DEFAULT_TICKET_WIDTH = 40;

    /**
     * @var int
     */
    public $ancho;

    /**
     * @var string
     */
    public $barcode;

    /**
     * @var string
     */
    public $codserie;

    /**
     * @var int
     */
    public $cuerpo_fontsize;

    /**
     * @var int
     */
    public $formato_precio;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $idempresa;

    /**
     * @var int
     */
    public $idlogo;

    /**
     * @var string
     */
    public $nombre;

    /**
     * @var string
     */
    public $tipodocumento;

    /**
     * @var int
     */
    public $titulo_fontsize;

    /**
     * @var int
     */
    public $titulo_negrita;

    public function clear()
    {
        parent::clear();

        $this->titulo_fontsize = self::DEFAULT_TITLE_FONTSIZE;
        $this->cuerpo_fontsize = self::DEFAULT_BODY_FONTSIZE;
        $this->ancho = self::DEFAULT_TICKET_WIDTH;
    }

    public static function primaryColumn(): string
    {
        return 'id';
    }

    public function primaryDescriptionColumn(): string
    {
        return 'nombre';
    }

    public static function tableName(): string
    {
        return 'formatos_tickets';
    }

    public function test()
    {
        $this->cuerpo_fontsize = $this->cuerpo_fontsize ?? self::DEFAULT_BODY_FONTSIZE;
        $this->titulo_fontsize = $this->titulo_fontsize ?? self::DEFAULT_TITLE_FONTSIZE;

        return parent::test();
    }

    public function url(string $type = 'auto', string $list = 'EditTicketsFormat?activetab=List'): string
    {
        return parent::url($type, $list);
    }

    /**
     * @param string $type
     * @return FormatoTicket[]
     */
    public function allFromDocument(string $type): array
    {
        $where = [
            new DataBaseWhere('tipodocumento', $type, '='),
            new DataBaseWhere('tipodocumento', NULL, 'IS', 'OR'),
        ];

        return $this->all($where, ['nombre' => 'ASC']);
    }

    public function loadFromDocument(string $type)
    {
        $where = [
            new DataBaseWhere('tipodocumento', $type, '='),
        ];

        return $this->loadFromCode('', $where);
    }
}
