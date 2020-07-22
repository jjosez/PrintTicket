<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Core\Model\Base\BusinessDocument;

/**
 *
 */
abstract class DocumentTemplate extends BaseTicketTemplate
{
    protected $document;
    protected $headLines;
    protected $footLines;

    public function __construct($width = '50')
    {
        parent::__construct($width);

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
