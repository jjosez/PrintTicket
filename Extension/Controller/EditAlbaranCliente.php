<?php

namespace FacturaScripts\Plugins\PrintTicket\Extension\Controller;

use Closure;

/**
 * @method addButton(string $string, string[] $array)
 */
class EditAlbaranCliente
{
    public function createViews(): Closure
    {
        return function () {
            $this->addButton('main', [
                'action' => 'ticketPrinterAction()',
                'color' => 'warning',
                'icon' => 'fas fa-print',
                'label' => 'print-ticket',
                'type' => 'js'
            ]);
        };
    }
}
