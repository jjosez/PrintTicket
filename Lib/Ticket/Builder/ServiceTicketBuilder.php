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


use FacturaScripts\Plugins\Servicios\Model\ServicioAT;

class ServiceTicketBuilder extends AbstractTicketBuilder
{
    protected $servicio;

    public function __construct(ServicioAT $servicio, int $width, bool $hidePrices = false)
    {
        parent::__construct($width);

        $this->servicio = $servicio;
        $this->hidePrices = $hidePrices;
        $this->ticketType = 'Servicio';
    }

    protected function buildHeader(): void
    {
        $this->printer->lineBreak();
        $company = $this->servicio->getCompany();

        $this->printer->lineSplitter();
        $this->printer->text($company->nombrecorto, true, true);
        $this->printer->bigText($company->direccion, true, true);

        if ($company->telefono1) {
            $this->printer->text('TEL: ' . $company->telefono1, true, true);
        }

        $this->printer->text($company->cifnif, true, true);
        $this->printer->LineSplitter('=');
    }

    protected function buildBody(): void
    {
        $this->printer->keyValueText('Servicio No.', $this->servicio->idservicio);
        $this->printer->keyValueText('Cliente ', $this->servicio->codcliente);
        $this->printer->keyValueText('Fecha', $this->servicio->fecha);
        $this->printer->keyValueText('Hora', $this->servicio->hora);
        $this->printer->lineSplitter('=');

        $this->printer->text('Descripcion: ');
        $this->printer->bigText($this->servicio->descripcion);
        $this->printer->lineBreak();

        $this->printer->text('Observaciones: ');
        $this->printer->bigText($this->servicio->observaciones);
        $this->printer->lineSplitter('=');

        $this->buildBodyDetail();
        $this->printer->lineSplitter('=');
    }

    protected function buildBodyDetail(): void
    {
        $this->printer->text('TRABAJOS', true, true);
        $this->printer->lineSplitter();

        foreach ($this->servicio->getTrabajos() as $trabajo) {
            $this->printer->keyValueText('Inicio:', $trabajo->fechainicio . ' ' . $trabajo->horainicio);
            $this->printer->keyValueText('Hasta:', $trabajo->fechafin . ' ' . $trabajo->horafin);

            $this->printer->lineBreak();
            $this->printer->text('Observaciones: ');
            $this->printer->bigText($trabajo->observaciones);

            $this->printer->lineBreak();
            $this->printer->text('Descripcion: ');
            $this->printer->bigText($trabajo->descripcion);
            $this->printer->lineSplitter();
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

        return $this->printer->output();
    }
}