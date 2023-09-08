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
use FacturaScripts\Dinamic\Model\Base\BusinessDocument;
use FacturaScripts\Dinamic\Model\FormatoTicket;
use FacturaScripts\Plugins\PrintTicket\Lib\PrintingService;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Builder\SalesTicket;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Builder\ServiceTicket;
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

    public function getPageData(): array
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

        $action = $this->request->request->get('action', '');
        if (true === $this->execAction($action)) {
            return;
        }

        $code = $this->request->request->get('codigo');
        $modelName = $this->request->request->get('tipo');

        if (true === empty($modelName) || true === empty($code)) {
            echo 'Not valid tikcet data';
            return;
        }

        if ('Servicio' === $modelName) {
            $this->sendServicePrintJob($code);
            return;
        }

        $this->sendPrintJob($modelName, $code, false);
    }

    /**
     * @param string $action
     * @return bool
     */
    protected function execAction(string $action): bool
    {
        switch ($action) {
            case 'get-formats':
                $this->response->setContent(json_encode($this->getFormatFromDocument()));
                return true;
            case 'print-document':
                $this->newPrintJob();
                return true;
            default:
                return false;
        }
    }

    /**
     * @param string $code
     * @return FormatoTicket
     */
    protected function getFormatFromCode(string $code = ''): FormatoTicket
    {
        $formato = new FormatoTicket();
        $formato->loadFromCode($code);

        return $formato;
    }

    /**
     * @return FormatoTicket[]
     */
    protected function getFormatFromDocument(): array
    {
        $tipoDocumento = $this->request->request->get('tipo-documento');
        $formato = new FormatoTicket();

        return $formato->allFromDocument($tipoDocumento);
    }

    /**
     * @return void
     */
    protected function newPrintJob(): void
    {
        $documentCode = $this->request->request->get('codigo');
        $formatCode = $this->request->request->get('formato');
        $modelName = $this->request->request->get('tipo');

        /** @var BusinessDocument $className */
        $className = self::MODEL_NAMESPACE . $modelName;
        $document = (new $className)->get($documentCode);

        if (false === $document) return;

        $ticketFormat = $this->getFormatFromCode($formatCode);
        $ticketBuilder = new SalesTicket($document, $ticketFormat);

        $salesTicket = new PrintingService($ticketBuilder);
        $salesTicket->savePrintJob();

        echo $salesTicket->getResponse();
    }

    protected function sendPrintJob($modelName, $code, bool $gift): void
    {
        $className = self::MODEL_NAMESPACE . $modelName;
        $document = (new $className)->get($code);

        if (false === $document) return;

        $ticketFormat = $this->getFormatFromCode($modelName);
        $ticketBuilder = new SalesTicket($document, $ticketFormat);

        $salesTicket = new PrintingService($ticketBuilder);
        $salesTicket->savePrintJob();

        echo $salesTicket->getResponse();
    }

    protected function sendServicePrintJob($code)
    {
        $servicio = (new ServicioAT())->get($code);

        if (false === $servicio) return;

        $ticketFormat = $this->getFormatFromCode('Servicio');
        $ticketBuilder = new ServiceTicket($servicio, $ticketFormat);

        $serviceTicket = new PrintingService($ticketBuilder);
        $serviceTicket->savePrintJob();

        echo $serviceTicket->getResponse();
    }
}
