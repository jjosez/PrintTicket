<?php
/*
 * This file is part of PrintTicket plugin for FacturaScripts
 * Copyright (c) 2021.  Juan José Prieto Dzul <juanjoseprieto88@gmail.com>
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
namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Builder;

use FacturaScripts\Dinamic\Model\FormatoTicket;
use FacturaScripts\Plugins\Servicios\Model\ServicioAT;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;

class ServiceTicket extends AbstractTicketBuilder
{
    /**
     * @var ServicioAT
     */
    protected $servicio;

    public function __construct(ServicioAT $servicio, ?FormatoTicket $formato)
    {
        parent::__construct($formato);

        $this->servicio = $servicio;
        $this->ticketType = 'Servicio';
    }

    protected function buildHeader(): void
    {
        $company = $this->servicio->getCompany();

        $this->printer->lineBreak();
        $this->setTitleFontStyle();

        $this->printLogo();
        $this->printer->textCentered($company->nombrecorto);
        $this->printer->textCentered($company->direccion);
        $this->printer->textCentered($company->telefono1);
        $this->printer->textCentered($company->cifnif);

        $this->resetFontStyle();
        $this->printer->lineSeparator('=');
    }

    protected function buildBody(): void
    {
        $this->printer->textKeyValue('Servicio No.', $this->servicio->idservicio);
        $this->printer->textKeyValue('Cliente ', $this->servicio->codcliente);
        $this->printer->textKeyValue('Fecha', $this->servicio->fecha);
        $this->printer->textKeyValue('Hora', $this->servicio->hora);
        $this->printer->lineSeparator('=');

        $this->printer->text('Descripcion: ');
        $this->printer->text($this->servicio->descripcion);
        $this->printer->lineBreak();

        $this->printer->text('Observaciones: ');
        $this->printer->text($this->servicio->observaciones);
        $this->printer->lineSeparator('=');

        $this->buildBodyDetail();
        $this->printer->lineSeparator('=');
    }

    protected function buildBodyDetail(): void
    {
        $this->printer->text('TRABAJOS', true, true);
        $this->printer->lineSeparator();

        foreach ($this->servicio->getTrabajos() as $trabajo) {
            $this->printer->textKeyValue('Inicio:', $trabajo->fechainicio . ' ' . $trabajo->horainicio);
            $this->printer->textKeyValue('Hasta:', $trabajo->fechafin . ' ' . $trabajo->horafin);

            $this->printer->lineBreak();
            $this->printer->text('Observaciones: ');
            $this->printer->text($trabajo->observaciones);

            $this->printer->lineBreak();
            $this->printer->text('Descripcion: ');
            $this->printer->text($trabajo->descripcion);


            $this->printer->textKeyValue('Cantidad:', $trabajo->cantidad);
            $this->printer->textKeyValue('Precio:', $trabajo->precio);
            $this->printer->lineSeparator();
        }
    }

    protected function buildFooter(): void
    {
        $this->printer->lineBreak(2);

        $this->printer->barcode($this->servicio->idservicio);
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        $this->buildHeader();
        $this->buildBody();
        $this->buildFooter();

        $this->printer->lineBreak(3);

        return $this->printer->getBuffer();
    }
}
