<?php
/**
 * This file is part of PrintTicket plugin for FacturaScripts
 * Copyright (C) 2018-2019 Juan José Prieto Dzul <juanjoseprieto88@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Plugins\PrintTicket\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Dinamic\Model\Ticket;
use FacturaScripts\Plugins\PrintTicket\Lib\CustomerServiceTicket;
use FacturaScripts\Plugins\PrintTicket\Lib\SalesTicket;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template\SalesTicketBuilder;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template\ServiceTicketBuilder;
use FacturaScripts\Plugins\Servicios\Model\ServicioAT;
use FacturaScripts\Core\App\AppSettings;

/**
 * Controller to generate a receipt from BusinessDocument Model.
 *
 * @author Juan José Prieto Dzul <juanjoseprieto88@gmail.com>
 */
class PrintTicket extends Controller
{
    const MODEL_NAMESPACE = '\\FacturaScripts\\Dinamic\\Model\\';

    public $document;

    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'Configuracion Tickets';
        $pageData['menu'] = 'admin';
        $pageData['icon'] = 'fas fa-print';
        $pageData['showonmenu'] = false;

        return $pageData;
    }

    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);
        $this->setTemplate(false);

        $code = $this->request->request->get('code');
        $gift = $this->request->request->get('gift');
        $modelName = $this->request->request->get('documento');

        if ('' === $modelName || '' === $code) {
            return;
        }

        if ('Servicio' === $modelName) {
            $this->saveServicePrintJob($code);
        } else {
            $this->sendPrintJob($modelName, $code, $gift);
        }
    }

    protected function sendPrintJob($modelName, $code, bool $gift)
    {
        $className = self::MODEL_NAMESPACE . $modelName;
        $document = (new $className)->get($code);

        if (false === $document) return;
        $ticketWidth = $this->getDefaulTicketWidth();
        $ticketBuilder = new SalesTicketBuilder($document, $ticketWidth, $gift);

        $salesTicket = new SalesTicket($ticketBuilder);
        $salesTicket->savePrintJob();

        echo $salesTicket->getMessage();
    }

    protected function saveServicePrintJob($code)
    {
        $servicio = (new ServicioAT())->get($code);

        if (false === $servicio) return;
        $ticketWidth = $this->getDefaulTicketWidth();
        $ticketBuilder = new ServiceTicketBuilder($servicio, $ticketWidth);

        $serviceTicket = new SalesTicket($ticketBuilder);
        $serviceTicket->savePrintJob();

        echo $serviceTicket->getMessage();
    }

    private function getDefaulTicketWidth(): int
    {
        return AppSettings::get('ticket', 'linelength', 50);
    }
}
