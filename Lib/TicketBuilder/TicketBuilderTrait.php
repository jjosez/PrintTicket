<?php 
namespace FacturaScripts\Plugins\PrintTicket\Lib\TicketBuilder;

use FacturaScripts\Core\Base\Translator;
/**
* Clase para imprimir tickets.
*/
trait TicketBuilderTrait
{
    use TicketWriterTrait;

    protected $ticket;
    protected $paperWidth;
    protected $disabledCommands;

    protected $document;
    protected $documentType;
    protected $company;

    protected $headerLines;
    protected $footerLines;
    protected $footerText;

    public function setCompany($company)
    {
        $this->company = $company;
    }

    public function setDocument($document, $documentType)
    {
        $this->document = $document;
        $this->documentType = $documentType;
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
        $this->writeBreakLine();

        $this->writeSplitter();
        $this->writeText($company->nombrecorto, true, true);
        $this->writeTextMultiLine($company->direccion, true, true);

        if ($company->telefono1) {
            $this->writeText('TEL: ' . $company->telefono1, true, true);
        }

        $this->writeBreakLine();
        $this->writeText($company->nombre, true, true);

        $i18n = new Translator();
        $cifnif = $i18n->trans('cifnif');

        $this->writeText($cifnif . ': ' . $company->cifnif, true, true);
        $this->writeSplitter('=');
    }

    protected function writeHeaderBlock($headerLines)
    {
        if ($headerLines) {
            foreach ($headerLines as $line) {
                $this->writeText($line, true, true);
            }
        }               
    }

    protected function writeBodyBlock($document, $documentType)
    {
        $text = strtoupper($documentType) . ' ' . $document->codigo;
        $this->writeText($text, true, true);

        $text = $document->fecha . ' ' . $document->hora;
        $this->writeText($text, true, true);

        $this->writeText("CLIENTE: " . $document->nombrecliente);
        $this->writeSplitter('=');
        $this->writeLabelValue('REFERENCIA','CANTIDAD');

        $totaliva=0;
        foreach ($document->getLines() as $linea) {
            $this->writeSplitter('=');
            $this->writeLabelValue($linea->referencia,$linea->cantidad);
            $this->writeText($linea->descripcion);
            $this->writeLabelValue('PVP:',$this->formatPrice($linea->pvpunitario));
            $this->writeLabelValue('IMPORTE:',$this->formatPrice($linea->pvptotal)); 

            $totaliva += $linea->pvptotal * $linea->iva / 100;            
        }

        $this->writeSplitter('=');
        $this->writeLabelValue('IVA',$this->formatPrice($totaliva));
        $this->writeLabelValue('TOTAL DEL DOCUMENTO:',$this->formatPrice($document->total));
    }

    protected function writeFooterBlock($footerLines, $leyenda, $codigo)
    {
        $this->writeBreakLine(2);

        if ($footerLines) {
            foreach ($footerLines as $line) {
                $this->writeText($line, true, true);
            }
        }

        $this->writeText($leyenda, true, true);
        $this->writeBarcode($codigo);
    }

    public function toString($open = false) : string
    {
        if ($open) {
            $this->openDrawer();
        }
        
        $this->writeCompanyBlock($this->company);
        $this->writeHeaderBlock($this->headerLines); 
        $this->writeBodyBlock($this->document, $this->documentType); 
        $this->writeFooterBlock($this->footerLines, $this->footerText, $this->document->codigo);      

        $this->writeBreakLine(4);
        $this->cutPaper();
        
        return $this->ticket;
    }
}