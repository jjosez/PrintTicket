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
use FacturaScripts\Core\Model\Base\SalesDocument;
use FacturaScripts\Dinamic\Model\FormatoTicket;
use FacturaScripts\Plugins\PrintTicket\Lib\PrintingService;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Builder\SalesTicket;

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
        }

        //$this->sendPrintJob($modelName, $code, false);
    }

    /**
     * @param string $action
     * @return bool
     */
    protected function execAction(string $action): bool
    {
        switch ($action) {
            case 'get-ticket-formats':
                $this->getTicketFormats();
                return true;
            case 'print-mobile-ticket':
                $this->printFromMobile();
                return true;
            case 'print-desktop-ticket':
                $this->printFromDesktop();
                return true;
            default:
                return false;
        }
    }

    protected function printFromMobile()
    {
        $ticketBuilder = $this->getSalesTicketBuilder();

        if (null === $ticketBuilder) {
            return;
        }

        $buffer = $ticketBuilder->getResult();

        echo "intent:base64," . base64_encode($buffer) . "#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;";
    }

    protected function printFromDesktop()
    {
        $ticketBuilder = $this->getSalesTicketBuilder();

        if (null === $ticketBuilder) {
            return;
        }

        $response = [
            'print_job_id' => PrintingService::newPrintJob($ticketBuilder)
        ];

        echo json_encode($response);
    }

   /* protected function sendPrintJob($modelName, $code): void
    {
        $className = self::MODEL_NAMESPACE . $modelName;
        $document = (new $className)->get($code);

        if (false === $document) return;

        $ticketFormat = $this->getFormatFromCode($modelName);
        $ticketBuilder = new SalesTicket($document, $ticketFormat);

        $salesTicket = new PrintingService($ticketBuilder);
        $salesTicket->savePrintJob();

        echo $salesTicket->getResponse();
    }*/

    /*protected function sendServicePrintJob($code)
    {
        $servicio = (new ServicioAT())->get($code);

        if (false === $servicio) return;

        $ticketFormat = $this->getFormatFromCode('Servicio');
        $ticketBuilder = new ServiceTicket($servicio, $ticketFormat);

        $serviceTicket = new PrintingService($ticketBuilder);
        $serviceTicket->savePrintJob();

        echo $serviceTicket->getResponse();
    }*/

    protected function getSalesTicketBuilder(): ?SalesTicket
    {
        $documentCode = $this->request->request->get('codigo');
        $formatCode = $this->request->request->get('formato');
        $modelName = $this->request->request->get('tipo');

        $className = self::MODEL_NAMESPACE . $modelName;
        $document = (new $className)->get($documentCode);

        if (!($document instanceof SalesDocument)) {
            return null;
        }

        $ticketFormat = $this->getFormatFromCode($formatCode);
        return new SalesTicket($document, $ticketFormat);
    }

    protected function getFormatFromCode(string $code = ''): FormatoTicket
    {
        $formato = new FormatoTicket();
        $formato->loadFromCode($code);

        return $formato;
    }

    protected function getTicketFormats(): void
    {
        $tipoDocumento = $this->request->request->get('tipo-documento');
        $formato = new FormatoTicket();

        $this->response->setContent(json_encode($formato->allFromDocument($tipoDocumento)));
    }
}
