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
    const PRINTER_PORT = 8089;

    /**
     * @var AbstractTicketBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $jsonResponse;

    public function __construct(AbstractTicketBuilder $ticketBuilder)
    {
        $this->builder = $ticketBuilder;
    }

    /**
     * @return mixed
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getResponse(): string
    {
        return $this->jsonResponse;
    }


    public function savePrintJob(): void
    {
        $ticket = new Ticket();

        $ticket->coddocument = $this->builder->getTicketType() . bin2hex(random_bytes(5));
        $ticket->text = $this->builder->getResult();

        if (false === $ticket->save()) {
            $this->setErrorMessage();
        } else {
            $this->setMessage($ticket->coddocument);
        }

        $this->setResponse($ticket->coddocument);
    }

    private function setResponse(string $code): void
    {
        $printMessage = (new Translator())->trans('printing');
        $documentType = (new Translator())->trans($this->builder->getTicketType());

        $response = [
            "message" => $printMessage,
            "document" => $documentType,
            "code" => $code
        ];

        $this->jsonResponse = json_encode($response);
    }

    private function setMessage(string $code): void
    {
        $this->message = (new Translator())->trans(
            'printing-ticket-comand',
            ['%code%' => $code, '%port%' => self::PRINTER_PORT]
        );
    }

    private function setErrorMessage()
    {
        $this->message = (new Translator())->trans('error-printing-ticket');
    }
}
