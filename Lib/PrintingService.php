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

use FacturaScripts\Core\Base\Translator;
use FacturaScripts\Dinamic\Model\Ticket;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Builder\AbstractTicketBuilder;

class PrintingService
{
    /**
     * @var
     */
    protected $message;

    protected $ticketBuilder;

    public function __construct(AbstractTicketBuilder $ticketBuilder)
    {
        $this->ticketBuilder = $ticketBuilder;
    }

    /**
     * @return mixed
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function savePrintJob(): void
    {
        $ticket = new Ticket();

        $ticket->coddocument = $this->ticketBuilder->getTicketType();
        $ticket->text = $this->ticketBuilder->getResult();

        if (false === $ticket->save()) {
            $this->message = 'Error al imprimir el ticket';
        }

        $this->setMessage($ticket->coddocument);
    }

    private function setMessage(string $code): void
    {
        $printMessage = (new Translator())->trans('printing');
        $documentType = (new Translator())->trans($code);

        $response = [
            "message" => $printMessage,
            "document" => $documentType,
            "code" => $code
        ];

        $this->message = json_encode($response);
    }
}
