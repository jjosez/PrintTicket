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

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Lib\ExtendedController\PanelController;
/**
 * Controller to save general settings to use generating receipts.
 *
 * @author Juan José Prieto Dzul <juanjoseprieto88@gmail.com>
 */
class EditTicketSettings extends PanelController
{
    public function getPageData()
    {
        $pagedata = parent::getPageData();
        $pagedata['title'] = 'print-ticket-settings';
        $pagedata['menu'] = 'admin';
        $pagedata['icon'] = 'fas fa-print';

        return $pagedata;
    }

    protected function createViews()
    {
        $this->setTemplate('EditTicketSettings');

        $this->addHtmlView('TicketSettings', 'CommonTicketSettings', 'TicketCustomLine', 'common-ticket-settings', 'fas fa-print');
        $this->addEditListView('EditTicketHeadLine', 'TicketCustomLine', 'header-custom-lines', 'fas fa-list-ul');
        $this->addEditListView('EditTicketFootLine', 'TicketCustomLine', 'footer-custom-lines', 'fas fa-list-ul');

        $this->setSettings('TicketSettings', 'btnNew', false);
    }

    protected function execAfterAction($action)
    {
        switch ($action) {
            case 'save-settings':
                $this->saveSettings();
                break;

            default:
                parent::execAfterAction($action);
        }
    }

    protected function loadData($viewName, $view)
    {
        $order = ['documento' => 'ASC', 'idlinea' => 'DESC'];
        switch ($viewName) {
            case 'EditTicketFootLine':
                $where = [new DataBaseWhere('posicion', 'footer')];
                $view->loadData('', $where, $order);
                $this->hasData = true;
                break;

            case 'EditTicketHeadLine':
                $where = [new DataBaseWhere('posicion', 'header')];
                $view->loadData('', $where, $order);
                break;
            case 'TicketSettings':
                $this->hasData = true;
                break;

            default:
                break;
        }
    }

    private function saveSettings()
    {
        $appSettings = $this->toolBox()->appSettings();

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
    }
}
