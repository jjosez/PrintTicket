<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\ESCPOS;

/**
 * 
 */

class Printer
{
    private $width;
    private $output;
    
    function __construct(int $width = 45)
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
        $text = substr($this->cleanText($text), 0, $this->width);
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

    public function bigText(?string $text, $linebreak = true, $center = false)
    {
        $text = $this->cleanText($text);

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

    public function keyValueText(?string $label, ?string $value, string $align = '')
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

    public function logo(string $logoPath)
    {
        $logo = '';
        $logo .= chr(27) . chr(42) . chr(48);

        $this->output .= $logo;
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

    public function codepage(int $code = 0)
    {
        $this->output .= chr(27) . chr(116) . chr($code);
    }

    private function decodeText(string $string)
    {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $string);
    }

    private function cleanText(?string $string): string
    {
        if (null === $string) return '';

        $charArray = ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A',
            'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I',
            'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a',
            'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o',
            'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y',
            'þ' => 'b', 'ÿ' => 'y'];

        return strtr($string, $charArray);
    }
}
