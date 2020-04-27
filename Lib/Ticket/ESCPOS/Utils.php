<?php 

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\ESCPOS;

/**
 * 
 */
class Utils
{
    public static function centerText($word = '', $width) : string
    {
        if (!$width) {
            return '';
        }

        if (strlen($word) == $width) {
            return $word;
        } else if (strlen($word) < $width) {
            return self::centerTextAux($word, $width);
        }

        $result = '';
        $nword = '';
        foreach (explode(' ', $word) as $aux) {
            if ($nword == '') {
                $nword = $aux;
            } else if (strlen($nword) + strlen($aux) + 1 <= $width) {
                $nword = $nword . ' ' . $aux;
            } else {
                if ($result != '') {
                    $result .= "\n";
                }

                $result .= self::centerTextAux($nword, $width);
                $nword = $aux;
            }
        }
        if ($nword != '') {
            if ($result != '') {
                $result .= "\n";
            }

            $result .= self::centerTextAux($nword, $width);
        }

        return $result;
    }

    private static function centerTextAux($word = '', $width)
    {
        $symbol = " ";
        $middle = round($width / 2);
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
}
