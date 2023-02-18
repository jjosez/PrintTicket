<?php

namespace FacturaScripts\Plugins\PrintTicket\Extension\Controller;

use Closure;

/**
 * @method addButton(string $string, string[] $array)
 */
class EditServicioAT
{
    public function createViews(): Closure
    {
        return function () {
            $this->addButton($this->getMainViewName(), [
                'action' => 'ticketPrinterAction()',
                'color' => 'info',
                'icon' => 'fas fa-print',
                'label' => 'print-ticket',
                'type' => 'js'
            ]);
        };
    }
}
