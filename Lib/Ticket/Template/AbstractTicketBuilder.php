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

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Model\TicketCustomLine;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\ESCPOS\Printer;

abstract class AbstractTicketBuilder
{
    /**
     * @var Printer
     */
    protected $printer;

    /**
     * @var string
     */
    protected $ticketType = 'general';

    public function __construct(int $width)
    {
        $this->printer = new Printer($width);
    }

    /**
     * @return string
     */
    public function getTicketType(): string
    {
        return $this->ticketType;
    }

    abstract protected function buildHeader(): void;

    abstract protected function buildBody(): void;

    abstract protected function buildFooter(): void;

    /**
     * @return string
     */
    abstract public function getResult(): string;

    /**
     *  Get custom lines for the given ticket type and  position
     *
     * @param string $position
     * @return array
     */
    protected function getCustomLines(string $position): array
    {
        return TicketCustomLine::rawFromDocument($this->ticketType, $position);
    }
}
