<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use _HumbugBox3ab8cff0fda0\___PHPSTORM_HELPERS\this;
use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Lib\Ticket\ESCPOS\Printer;
use FacturaScripts\Dinamic\Model\Empresa;

/**
 *
 */
abstract class BaseTicketTemplate
{
    protected $empresa;
    protected $printer;

    public function __construct(Empresa $empresa, $width = null)
    {
        $this->empresa = $empresa ?: new Empresa();
        $this->printer = $width ? new Printer($width) : new Printer($this->defaultWitdh());
    }

    protected function cutPapperCommand(bool $cut)
    {
        if (true === $cut) {
            $this->printer->cut();
            return;
        }
    }

    protected function openDrawerCommand(bool $open)
    {
        if (true === $open) {
            $this->printer->open();
            return;
        }
    }

    protected function defaultWitdh()
    {
        return AppSettings::get('ticket', 'linelength', 50);
    }
}
