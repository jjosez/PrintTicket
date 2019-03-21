<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib\TicketBuilder;

use FacturaScripts\Core\Base\Translator;
use FacturaScripts\Core\Base\DivisaTools;

class TicketTemplateMaster
{
    protected $document;
    protected $documentTitle;
    protected $footerLines;
    protected $footerText;
    protected $gift;
    protected $headerLines;
    protected $i18n;    
    protected $ticket; 

    protected static $divisaTools; 

    function __construct($document, $width = 45, $gift = false, $opendrawer = true, $cutpaper = true) 
    {
        $this->document = $document;
        $this->footerLines = [];
        $this->headerLines = []; 
        $this->gift = $gift;

        $this->i18n = new Translator();
        $this->ticket = new POSTicketBuilder($width , $opendrawer, $cutpaper); 

        static::$divisaTools = new DivisaTools();
    }

    public function setCustomHeaderLines($headerLines)
    {        
        foreach ($headerLines as $line) {
            $this->headerLines[] = $line->texto;
        }
    }

    public function setCustomFooterLines($footerLines)
    {
        foreach ($footerLines as $line) {
            $this->footerLines[] = $line->texto;
        }
    }

    public function setFooterText($footerText)
    {
        $this->footerText = $footerText;
    }

    protected function writeCompanyBlock($company)
    {
        $this->ticket->addLineBreak();

        $this->ticket->addSplitter();
        $this->ticket->addText($company->nombrecorto, true, true);
        $this->ticket->addTextMultiLine($company->direccion, true, true);

        if ($company->telefono1) {
            $this->ticket->addText('TEL: ' . $company->telefono1, true, true);
        }

        $this->ticket->addLineBreak();
        $this->ticket->addText($company->nombre, true, true);
        
        $cifnif = $this->i18n->trans('cifnif');

        $this->ticket->addText($cifnif . ': ' . $company->cifnif, true, true);
        $this->ticket->addSplitter('=');
    }

    protected function writeHeaderBlock($headerLines)
    {
        if ($headerLines) {
            foreach ($headerLines as $line) {
                $this->ticket->addText($line, true, true);
            }
        }               
    }

    protected function writeBodyBlock($document)
    {
        $text = $this->documentTitle . ' ' . $document->codigo;
        $this->ticket->addText($text, true, true);

        $text = $document->fecha . ' ' . $document->hora;
        $this->ticket->addText($text, true, true);

        $this->ticket->addText("CLIENTE: " . $document->nombrecliente);
        $this->ticket->addSplitter('=');
        $this->ticket->addLabelValueText('REFERENCIA','CANTIDAD');
        $this->ticket->addSplitter('=');

        $totaliva=0;  
        foreach ($document->getLines() as $linea) {            
            $this->ticket->addLabelValueText($linea->referencia,$linea->cantidad);
            $this->ticket->addText($linea->descripcion);          

            if (!$this->gift) {
                $this->ticket->addLabelValueText('PVP:', static::$divisaTools->format($linea->pvpunitario));
                $this->ticket->addLabelValueText('IMPORTE:', static::$divisaTools->format($linea->pvptotal));
            }             

            $totaliva += $linea->pvptotal * $linea->iva / 100;            
        }

        $this->ticket->addSplitter('=');
        if ($this->gift) {
            $this->ticket->addText('Ticket de regalo', true, true);
        } else {            
            $this->ticket->addLabelValueText('IVA', static::$divisaTools->format($totaliva));
            $this->ticket->addLabelValueText('TOTAL DEL DOCUMENTO:', static::$divisaTools->format($document->total));
        } 
    }

    protected function writeFooterBlock($footerLines, $leyenda, $codigo)
    {
        $this->ticket->addLineBreak(2);

        if ($footerLines) {
            foreach ($footerLines as $line) {
                $this->ticket->addText($line, true, true);
            }
        }

        $this->ticket->addText($leyenda, true, true);
        $this->ticket->addBarcode($codigo);
    }

    public function toString() : string
    {        
        $this->writeCompanyBlock($this->document->getCompany());
        $this->writeHeaderBlock($this->headerLines); 
        $this->writeBodyBlock($this->document); 
        $this->writeFooterBlock($this->footerLines, $this->footerText, $this->document->codigo);      

        $this->ticket->addLineBreak(4);
        
        return $this->ticket->getResult();
    }
}