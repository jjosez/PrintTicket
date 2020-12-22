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
use FacturaScripts\Dinamic\Lib\SalesDocumentTicket;
use FacturaScripts\Dinamic\Model\Ticket;
use FacturaScripts\Plugins\PrintTicket\Lib\CustomerServiceTicket;
use FacturaScripts\Plugins\Servicios\Model\ServicioAT;

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
        $this->setTemplate('PrintTicketScreen');

        //$code = $this->request->query->get('code');
        $code = $this->request->request->get('code');
        $modelName = $this->request->request->get('documento');

        if ('' === $modelName || '' === $code) {
            return;
        }

        if ('Servicio' === $modelName) {
            $this->saveServicePrintJob($code);
        } else {
            $this->savePrintJob($modelName, $code);
        }
    }

    protected function savePrintJob($modelName, $code)
    {
        $className = self::MODEL_NAMESPACE . $modelName;
        $document = (new $className)->get($code);

        if (false === $document) return;

        $businessTicket = new SalesDocumentTicket($document, $modelName);

        $ticket = new Ticket();
        $ticket->coddocument = $this->document = $document->modelClassName();
        $ticket->text = $businessTicket->getTicket();

        if (!$ticket->save()) {
            echo 'Error al guardar el ticket';
        }
    }

    protected function saveServicePrintJob($code)
    {
        $document = (new ServicioAT())->get($code);

        if (false === $document) return;

        $businessTicket = new CustomerServiceTicket($document);

        $ticket = new Ticket();
        $ticket->coddocument = $this->document = 'Servicio';
        $ticket->text = $businessTicket->getTicket();

        if (!$ticket->save()) {
            echo 'Error al guardar el ticket';
        }
    }
}
