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

use FacturaScripts\Dinamic\Model\AttachedFile;
use FacturaScripts\Dinamic\Model\FormatoTicket;
use FacturaScripts\Dinamic\Model\TicketCustomLine;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\TicketPrinter;

abstract class AbstractTicketBuilder
{
    const PRICE_AFTER_TAX = 1;
    const PRICE_BEFORE_TAX = 2;
    const PRICE_NO_PRICE = 3;
    /**
     * @var TicketPrinter
     */
    protected $printer;

    /**
     * @var FormatoTicket
     */
    protected $formato;

    /**
     * @var string
     */
    protected $ticketType = 'general';

    public function __construct(?FormatoTicket $formato)
    {
        $this->formato = $formato ?? new FormatoTicket();

        $this->printer = new TicketPrinter($this->formato->ancho);
    }

    /**
     * @return string
     */
    public function getTicketType(): string
    {
        return $this->ticketType;
    }

    /**
     * @return string
     */
    abstract public function getResult(): string;

    /**
     * @return int
     */
    protected function getPriceFormat(): int
    {
        return $this->formato->formato_precio;
    }

    /**
     * @return bool
     */
    protected function hasTitleBold(): bool
    {
        return $this->formato->titulo_negrita;
    }

    protected function resetFontStyle()
    {
        $this->printer->getPrinterEngine()->setEmphasis(false);
        $this->printer->getPrinterEngine()->setTextSize(1, 1);
    }

    protected function setBodyFontSize()
    {
        $size = $this->formato->cuerpo_fontsize;
        $this->printer->getPrinterEngine()->setTextSize($size, $size);
    }

    protected function setTitleFontStyle()
    {
        if (true === $this->formato->titulo_negrita) {
            $this->printer->getPrinterEngine()->setEmphasis();
        }

        $size = $this->formato->titulo_fontsize;
        $this->printer->getPrinterEngine()->setTextSize($size, $size);
    }

    abstract protected function buildHeader(): void;

    abstract protected function buildBody(): void;

    abstract protected function buildFooter(): void;

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

    protected function printBarcode($text): void
    {
        switch ($this->formato->barcode) {
            case 'barcode':
                $this->printer->barcode($text);
                break;
            case 'qr':
                $this->printer->qrcode($text);
                break;
        }
    }

    protected function printLogo()
    {
        if (true === empty($this->formato->idlogo)) {
            return;
        }

        $logo = new AttachedFile();
        if (false === $logo->loadFromCode($this->formato->idlogo)) {
            return;
        }

        if (false === $this->isValidLogoFile($logo->path)) {
            return;
        }

        $this->printer->logo($logo->path);
    }

    /*protected function printLogo()
    {
        $logo = new AttachedFile();
        $logoFilePath =  FS_FOLDER . '/' . 'Dinamic/Assets/Images/img.png';

        echo $logoFilePath;

        if (!file_exists($logoFilePath)) {
            return;
        }

        $this->printer->logo($logoFilePath);
    }*/

    protected function isValidLogoFile(?string $path): bool
    {
        if (false === file_exists($path)) {
            return false;
        }

        if ('image/png' === mime_content_type($path) || 'image/jpeg' === mime_content_type($path)) {
            return true;
        }

        return false;
    }
}
