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

use FacturaScripts\Core\Base\NumberTools;
use FacturaScripts\Core\Model\Base\BusinessDocument;

class SalesTicketBuilder extends AbstractTicketBuilder
{
    /**
     * @var BusinessDocument
     */
    protected $document;

    /**
     * @var bool
     */
    protected $hidePrices;

    public function __construct(BusinessDocument $document, int $width, bool $hidePrices = false)
    {
        parent::__construct($width);

        $this->document = $document;
        $this->hidePrices = $hidePrices;
        $this->ticketType = $document->modelClassName();
    }

    /**
     * Builds the ticket head
     */
    protected function buildHeader(): void
    {
        $company = $this->document->getCompany();
        $this->printer->lineBreak();

        $this->printer->lineSplitter();
        $this->printer->text($company->nombrecorto, true, true);
        $this->printer->text($company->nombre, true, true);
        $this->printer->bigText($company->direccion, true, true);

        if ($company->telefono1) {
            $this->printer->text('TEL: ' . $company->telefono1, true, true);
        }

        $this->printer->text($company->cifnif, true, true);
        $this->printer->LineSplitter('=');

        foreach ($this->getCustomLines('header') as $line) {
            $this->printer->text($line, true, true);
        }
    }

    /**
     * Builds the ticket body
     */
    protected function buildBody(): void
    {
        $this->printer->text($this->document->codigo, true, true);
        $fechacompleta = $this->document->fecha . ' ' . $this->document->hora;
        $this->printer->text($fechacompleta, true, true);

        $this->printer->text('CLIENTE: ' . $this->document->nombrecliente);
        $this->printer->lineSplitter('=');

        $this->printer->text('ARTICULO');
        $columnas = $this->printer->columnText(3, 'CANTIDAD');
        $columnas .= $this->printer->columnText(3, 'UNITARIO');
        $columnas .= $this->printer->columnText(3, 'IMPORTE');
        $this->printer->text($columnas);
        $this->printer->lineSplitter('=');

        foreach ($this->document->getLines() as $line) {
            $this->printer->text("$line->referencia - $line->descripcion");

            $desglose = $this->printer->columnText(3, $line->cantidad);

            if (false === $this->hidePrices) {
                $desglose .= $this->printer->columnText(3, NumberTools::format($line->pvpunitario));
                $desglose .= $this->printer->columnText(3, NumberTools::format($line->pvpsindto));
                $this->printer->text($desglose);

                $descuento = $line->pvpsindto - ($line->pvpsindto * $line->getEUDiscount());
                $this->printer->keyValueText('Descuento:', '- ' . NumberTools::format($descuento));

                $impuestoLinea = $line->pvptotal * $line->iva / 100;
                $this->printer->keyValueText("Impuesto $line->iva%:", '+ ' . NumberTools::format($impuestoLinea));
                $this->printer->keyValueText('Total linea:', NumberTools::format($line->pvptotal + $impuestoLinea));
            } else {
                $this->printer->text($desglose);
            }

            $this->printer->lineBreak();
        }

        if (false === $this->hidePrices) {
            $this->printer->lineSplitter('=');
            $this->printer->keyValueText('BASE', NumberTools::format($this->document->neto));
            $this->printer->keyValueText('IVA', NumberTools::format($this->document->totaliva));
            $this->printer->keyValueText('TOTAL DEL DOCUMENTO:', NumberTools::format($this->document->total));
        }
    }

    /**
     * Builds the ticket foot
     */
    protected function buildFooter(): void
    {
        $this->printer->lineBreak(2);

        foreach ($this->getCustomLines('footer') as $line) {
            $this->printer->bigText($line, true, true);
        }

        $this->printer->lineBreak(2);
        $this->printer->barcode($this->document->codigo);
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
