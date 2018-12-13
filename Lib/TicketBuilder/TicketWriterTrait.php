<?php 
namespace FacturaScripts\Plugins\PrintTicket\Lib\TicketBuilder;

/**
* Clase pare imprimir tickets.
*/
trait TicketWriterTrait
{   
    public function writeText($text = '', $linebreake = TRUE, $center = FALSE)
    {
        $text = substr($text, 0, $this->paperWidth);
        if ($text != '') {
            if ($center) {
                $this->ticket .= $this->centerText($text);
            } else {
                $this->ticket .= $text;
            }            
        } 
        if ($linebreake) {
            $this->ticket .= "\n";
        }                
    }

    public function writeTextBold($text = '', $brake = TRUE, $center = FALSE)
    {
        $text = substr($text, 0, $this->paperWidth);
        $text = chr(27) . chr(69) . chr(49) . $text . chr(27) . chr(69) . chr(48);
        if ($text != '') {
            if ($center) {
                $this->ticket .= $this->centerText($text);
            } else {
                $this->ticket .= $text;
            }            
        } 
        if ($brake) {
            $this->writeBreakLine();
        }           
    }

    public function writeTextMultiLine($text = '', $linebreake = TRUE, $center = FALSE)
    {
        if ($text != '') {
            if ($center) {
                $this->ticket .= $this->centerText($text);
            } else {
                $this->ticket .= $text;
            }            
        } 
        if ($linebreake) {
            $this->ticket .= "\n";
        }                
    }

    public function writeBreakLine($n = 1)
    {
        for ($i=0; $i < $n; $i++) { 
            $this->ticket .= "\n";
        }        
    }

    public function writeSplitter($splitter = '-')
    {
        $line = '';
        for ($i = 0; $i < $this->paperWidth; $i++) {
            $line .= $splitter;
        }

        $this->ticket .= $line . "\n";
    }

    public function writeLabelValue($label, $value, $align = '')
    {
        $texto = $label;
        $ancho = $this->paperWidth - strlen($label);

        $value = substr($value, 0, $ancho);
        $texto .= sprintf('%' . $align . $ancho . 's', $value);

        $this->ticket .= $texto;
        $this->writeBreakLine();
    }

    public function writeBarcode($text = '')
    {
        $barcode = '';
        $barcode .= chr(27) . chr(97) . chr(49); #justification n=0,48 left n=2,49 center n=3,50 right
        $barcode .= chr(29) . chr(104) . chr(70); #barcode height in dots n=100, 1 < n < 255
        $barcode .= chr(29) . chr(119) . chr(2); #barcode width multiplier n=1, n=2, 
        $barcode .= chr(29) . chr(72) . chr(50); #barcode HRI position n=1,49 above n=2,50 below 
        $barcode .= chr(29) . chr(107) . chr(4) . $text . chr(0);
        $this->ticket .= $barcode;
    }

    public function centerText($word = '', $ancho = FALSE)
    {
        if (!$ancho) {
            $ancho = $this->paperWidth;
        }

        if (strlen($word) == $ancho) {
            return $word;
        } else if (strlen($word) < $ancho) {
            return $this->centerTextAux($word, $ancho);
        }

        $result = '';
        $nword = '';
        foreach (explode(' ', $word) as $aux) {
            if ($nword == '') {
                $nword = $aux;
            } else if (strlen($nword) + strlen($aux) + 1 <= $ancho) {
                $nword = $nword . ' ' . $aux;
            } else {
                if ($result != '') {
                    $result .= "\n";
                }

                $result .= $this->centerTextAux($nword, $ancho);
                $nword = $aux;
            }
        }
        if ($nword != '') {
            if ($result != '') {
                $result .= "\n";
            }

            $result .= $this->centerTextAux($nword, $ancho);
        }

        return $result;
    }

    private function centerTextAux($word = '', $ancho = 40)
    {
        $symbol = " ";
        $middle = round($ancho / 2);
        $length_word = strlen($word);
        $middle_word = round($length_word / 2);
        $last_position = $middle + $middle_word;
        $number_of_spaces = $middle - $middle_word;
        $result = sprintf("%'{$symbol}{$last_position}s", $word);
        for ($i = 0; $i < $number_of_spaces; $i++) {
            $result .= "$symbol";
        }
        return $result;
    }

    protected function formatPrice($val, $decimales = 2, $moneda = false)
    {
        $val = sqrt($val ** 2);
        if ($moneda) {
            return '$ ' . number_format($val, $decimales, '.', '');
        }
        return number_format($val, $decimales, '.', '');
    }

    public function cutPaper()
    {
        if ($this->commandToCut) {
            $aux = explode('.', $this->commandToCut);
            if ($aux) {
                foreach ($aux as $a) {
                    $this->ticket .= chr($a);
                }

                $this->writeBreakLine();
            }
        } 
    }

    public function openDrawer()
    {
        if (!$this->disabledCommands) {            
            $aux = explode('.', $this->commandToOpen);
            if ($aux) {
                foreach ($aux as $a) {
                    $this->ticket .= chr($a);
                }

                $this->ticket .= "\n";
            }            
        }
        return $this->ticket;
    }
}