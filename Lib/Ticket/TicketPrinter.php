<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket;

use FacturaScripts\Core\Base\ToolBox;
use FacturaScripts\Core\Tools;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\PrintConnectors\PrintConnector;
use Mike42\Escpos\Printer;

class TicketPrinter
{
    public const KEY_VALUE_ALIGN_LEFT = STR_PAD_RIGHT;
    public const KEY_VALUE_ALIGN_RIGHT = STR_PAD_LEFT;
    public const KEY_VALUE_ALIGN_CENTER = STR_PAD_BOTH;

    /**
     * @var PrintConnector
     */
    private $connector;

    /**
     * @var Printer
     */
    private $printer;

    /**
     * @var int
     */
    private $width;

    public function __construct(int $width)
    {
        $this->connector = new DummyPrintConnector();
        $this->printer = new Printer($this->connector);
        $this->width = $width;
    }

    public function getBuffer(): string
    {
        $output = $this->connector->getData();
        $this->printer->close();

        return $output;
    }

    public function getPrinterEngine(): Printer
    {
        return $this->printer;
    }

    public function barcode(?string $code)
    {
        if (true === empty($code)) return;

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);

        $this->printer->barcode($code);

        $this->printer->setJustification();
    }

    public function qrcode(?string $code)
    {
        if (true === empty($code)) return;

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);

        $this->printer->qrCode($code, Printer::QR_ECLEVEL_L, 6);

        $this->printer->setJustification();
    }

    public function lineBreak(int $n = 1)
    {
        /*for ($i = 0; $i < $n; $i++) {
            $this->printer->text("\n");
        }*/
        $break = str_repeat("\n", $n);
        $this->printer->text($break);
    }

    public function lineFeed(int $n = 1)
    {
        $this->printer->feed($n);
    }

    public function lineSeparator(string $splitter = '-')
    {
        /*$line = '';
        for ($i = 0; $i < $this->width; $i++) {
            $line .= $splitter;
        }*/

        $separator = str_repeat($splitter, $this->width);

        $this->printer->text($separator);
        $this->lineBreak();
    }

    public function logo(string $path)
    {
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);

        $logo = EscposImage::load($path);
        $this->printer->graphics($logo);

        $this->printer->setJustification();
    }

    public function setFontBold($bold = true)
    {
        $this->printer->setEmphasis($bold);
    }

    public function text(?string $text, bool $linebreak = true)
    {
        if (true === empty($text)) return;

        $text = ToolBox::utils()::normalize($text);
        $this->printer->text($text);

        if (true === $linebreak) $this->lineBreak();
    }

    public function textBold(?string $text, bool $linebreak = true, bool $center = false)
    {
        if (true === empty($text)) return;

        $this->setJustification($center);
        $this->printer->setEmphasis();

        $text = ToolBox::utils()::normalize($text);
        $this->printer->text($text);

        if (true === $linebreak) $this->lineBreak();
        $this->printer->setEmphasis(false);
    }

    public function textCentered(?string $text, bool $linebreak = true)
    {
        if (true === empty($text)) return;

        $this->printer->setJustification(Printer::JUSTIFY_CENTER);

        $this->text($text, $linebreak);

        $this->printer->setJustification();
    }

    /**
     * @param string|null $key
     * @param string|null $value
     * @param string $keyAlign KV_ALING_LEFT, KV_ALING_RIGHT, KV_ALING_CENTER
     * @param string $valueAlign KV_ALING_LEFT, KV_ALING_RIGHT, KV_ALING_CENTER
     * @return void
     */
    public function textKeyValue(
        ?string $key,
        ?string $value,
        string  $keyAlign = self::KEY_VALUE_ALIGN_LEFT,
        string  $valueAlign = self::KEY_VALUE_ALIGN_RIGHT
    ) {
        $width = (int) ($this->width / 2);

        $key = ToolBox::utils()::normalize($key);
        $text = str_pad($key ?? '', $width, ' ', $keyAlign);
        $this->printer->text(ToolBox::utils()::normalize($text));

        $value = ToolBox::utils()::normalize($value);
        $text = str_pad($value ?? '', $width, ' ', $valueAlign);
        $this->printer->text(ToolBox::utils()::normalize($text));

        $this->lineBreak();
    }

    /**
     * @param string|null $left
     * @param string|null $right
     * @param string $align
     * @return void
     */
    public function text2Column(?string $left, ?string $right, string $align = '')
    {
        $text = $left;
        $width = $this->width - strlen($right);

        $value = substr($right, 0, $width);
        $text .= sprintf('%' . $align . $width . 's', $value);

        $this->printer->text($text);
        $this->lineBreak();
    }

    /**
     * @param int $cols
     * @param string $text
     * @param string $align '-': Align left, '': Align right
     * @return string
     */
    public function textToColumn(int $cols, string $text, string $align = ''): string
    {
        $width = intdiv($this->width, $cols);
        return sprintf('%' . $align . $width . 's', $text);
    }

    private function setJustification(bool $center)
    {
        if (true === $center) {
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            return;
        }

        $this->printer->setJustification();
    }

    private function getTextAlign(string $align = '')
    {
        switch ($align) {
            case 'L':
                return STR_PAD_RIGHT;
            case 'R':
                return STR_PAD_LEFT;
            default:
                return STR_PAD_BOTH;
        }
    }
}
