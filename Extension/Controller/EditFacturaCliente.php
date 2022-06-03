<?php

namespace FacturaScripts\Plugins\PrintTicket\Extension\Controller;

use Closure;

/**
 * @method addButton(string $string, string[] $array)
 */
class EditFacturaCliente
{
    public function createViews(): Closure
    {
        return function () {
            $this->addButton('main', [
                'action' => 'printTicketDialog()',
                'color' => 'info',
                'icon' => 'fas fa-print',
                'label' => 'print-ticket',
                'type' => 'js'
            ]);
        };
    }
}