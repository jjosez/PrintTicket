<?php

namespace FacturaScripts\Plugins\PrintTicket\Controller;

use FacturaScripts\Core\Lib\ExtendedController\EditController;

class EditFormatoTicket extends EditController
{
    public function getModelClassName(): string
    {
        return 'FormatoTicket';
    }

    /**
     * @return array
     */
    public function getPageData(): array
    {
        $pagedata = parent::getPageData();
        $pagedata['title'] = 'ticket-format';
        $pagedata['menu'] = 'admin';
        $pagedata['icon'] = 'fas fa-print';
        $pagedata['showonmenu'] = false;

        return $pagedata;
    }
}
