<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\ESCPOS;
use function transliterator_transliterate;

/**
 * 
 */

class Printer
{
    private $width;
    private $output;
    
    function __construct($width = '45')
    {
        $this->width = $width;
        $this->output = '';
    }

    public function output() : string
    {
        return $this->output;
    }

    public function text(string $text, $linebreak = true, $center = false) 
    {
        $text = substr($this->sanitizeText($text), 0, $this->width);
        if ($text != '') {
            if ($center) {
                $this->output .= Utils::centerText($text, $this->width);
            } else {
                $this->output .= $text;
            }            
        } 
        if ($linebreak) {
            $this->lineBreak();
        }  
    }

    public function bigText(string $text, $linebreak = true, $center = false)
    {
        $text = $this->sanitizeText($text);

        if ($text != '') {
            if ($center) {
                $this->output .= Utils::centerText($text, $this->width);
            } else {
                $this->output .= $text;
            }            
        } 
        if ($linebreak) {
            $this->lineBreak();
        } 
    }

    public function columnText(int $cols, string $text, string $align = '')
    {
        $width = intdiv($this->width, $cols);
        return sprintf('%' . $align . $width . 's', $text);
    }

    public function keyValueText(string $label, string $value, string $align = '')
    {
        $text = $label;
        $width = $this->width - strlen($label);

        $value = substr($value, 0, $width);
        $text .= sprintf('%' . $align . $width . 's', $value);

        $this->output .= $text;
        $this->lineBreak();
    }

    public function barcode(string $text)
    {
        $barcode = '';
        $barcode .= chr(27) . chr(97) . chr(49);  // justification n=0,48 left n=2,49 center n=3,50 right
        $barcode .= chr(29) . chr(104) . chr(70); // barcode height in dots n=100, 1 < n < 255
        $barcode .= chr(29) . chr(119) . chr(2);  // barcode width multiplier n=1, n=2,
        $barcode .= chr(29) . chr(72) . chr(50);  // barcode HRI position n=1,49 above n=2,50 below
        $barcode .= chr(29) . chr(107) . chr(4) . $text . chr(0);
        $this->output .= $barcode;
    }

    public function logo(string $command)
    {
        $chars = explode('.', $command);

        if ($chars) {
            $logocmd = '';
            foreach ($chars as $char) {
                $logocmd .= chr($char);
            }

            $this->output .= $logocmd;
        }

        $this->lineBreak(2);
    }

    public function cut()
    {
        $this->output .= '[[cut]]';
        $this->lineBreak();
    }

    public function open()
    {
        $this->output .= '[[opendrawer]]';
        $this->lineBreak();    
    }

    public function lineBreak($n = 1)
    {
        for ($i=0; $i < $n; $i++) { 
            $this->output .= "\n";
        }        
    }

    public function lineSplitter($splitter = '-')
    {
        $line = '';
        for ($i = 0; $i < $this->width; $i++) {
            $line .= $splitter;
        }

        $this->output .= $line;
        $this->lineBreak();
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width = 45)
    {
        $this->width = $width;
    }

    public function codepage(int $code = 0)
    {
        $this->output .= chr(27) . chr(116) . chr($code);
    }

    private function decodeText(string $string)
    {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $string);
    }

    private function sanitizeText(string $text)
    {
        //$text = utf8_encode($text);
        return transliterator_transliterate('Any-Latin; Latin-ASCII;', $text);
    }
}
