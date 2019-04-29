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

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\Base\DataBase;

use FacturaScripts\Dinamic\Model\TicketCustomLine;

/**
 * Controller to save general settings to use generating receipts.
 *
 * @author Juan José Prieto Dzul <juanjoseprieto88@gmail.com>
 */
class PrintTicketSettings extends Controller
{
    public $headerLines;
    public $footerLines;

    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'Configuracion de tickets';
        $pageData['menu'] = 'admin';
        $pageData['icon'] = 'fas fa-print';

        return $pageData;
    }

    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);
        $appSettings = new AppSettings();

        $footerText = $this->request->request->get('footertext');
        if ($footerText) {
            $appSettings->set('ticket', 'footertext', $footerText);
        }

        $lineLength = $this->request->request->get('linelength');
        if ($lineLength) {
            $appSettings->set('ticket', 'linelength', $lineLength);
        }

        $gift = ($this->request->request->get('gift')) ? true : false;
        $appSettings->set('ticket', 'gift', $gift);
        
        $appSettings->save();

        $action = $this->request->request->get('accion');

        switch ($action) {
            case 'save':
                $this->saveCustomLines();
                break;

            case 'delete':
                $this->deleteCustomLine();
                break;
            
            default:
                # code...
                break;
        }

        $this->headerLines = (new TicketCustomLine)->getFromDocument('general', 'header');
        $this->footerLines = (new TicketCustomLine)->getFromDocument('general', 'footer');
    }

    public function saveCustomLines()
    {
        $customLineID = $this->request->request->get('idlinea');
        $customLinePosition = $this->request->request->get('posicion');
        $customLineText = $this->request->request->get('texto');

        $line = new TicketCustomLine();
        $line->loadFromCode($customLineID);
        
        $line->documento = 'general';
        $line->posicion = $customLinePosition;
        $line->texto = $customLineText;
        $line->save();
    }

    public function deleteCustomLine()
    {
        $customLineID = $this->request->request->get('idlinea');

        $customLine = new TicketCustomLine();
        if ($customLine->loadFromCode($customLineID)) {
            $customLine->delete();
        }
    }
}
