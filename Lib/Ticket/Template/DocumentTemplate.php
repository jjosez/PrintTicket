<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Core\Model\Base\BusinessDocument;
use FacturaScripts\Dinamic\Model\Empresa;

/**
 *
 */
abstract class DocumentTemplate extends BaseTicketTemplate
{
    protected $document;
    protected $headLines;
    protected $footLines;

    public function __construct(Empresa $empresa, int $width)
    {
        parent::__construct($empresa, $width);

        $this->headLines = [];
        $this->footLines = [];
    }

    abstract public function buildTicket(
        BusinessDocument $document,
        array $headlines,
        array $footlines,
        bool $cut = true,
        bool $open = true
    ): string;
}
