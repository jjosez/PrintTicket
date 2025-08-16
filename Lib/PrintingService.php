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

namespace FacturaScripts\Plugins\PrintTicket\Lib;

use FacturaScripts\Dinamic\Model\Ticket;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Builder\AbstractTicketBuilder;

class PrintingService
{
    public const PRINTER_PORT = 8089;

    public static function saveTicket(string $ticketType, string $ticketBody): string
    {
        $ticket = new Ticket();

        $ticket->setPrintCode($ticketType);
        $ticket->text = $ticketBody;

        if ($ticket->save()) {
            return $ticket->coddocument;
        }

        return '';
    }

    public static function newPrintJob(AbstractTicketBuilder $builder): string
    {
        return self::saveTicket($builder->getTicketType(), $builder->getResult());
    }
}
