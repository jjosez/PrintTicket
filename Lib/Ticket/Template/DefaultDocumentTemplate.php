<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Core\Model\Base\BusinessDocument;

/**
 * 
 */
class DefaultDocumentTemplate extends DocumentTemplate
{

    public function __construct($width = '50')
    {
        parent::__construct($width);
    }

    public function buildTicket(
        BusinessDocument $document,
        array $headlines, 
        array $footlines,
        bool $cut = true,
        bool $open = true
    ) : string {
        $this->document = $document;
        $this->headLines = $headlines;
        $this->footLines = $footlines;

        $this->buildHead();
        $this->buildMain();
        $this->buildFoot();

        $this->printer->lineBreak();
        $this->openDrawerCommand($open);
        $this->cutPapperCommand($cut);

        return $this->printer->output();
    }

    protected function buildHead()
    {
        $company = $this->document->getCompany();
        $this->printer->lineBreak();

        $this->printer->lineSplitter();
        $this->printer->text($company->nombrecorto, true, true);
        $this->printer->bigText($company->direccion, true, true);

        if ($company->telefono1) {
            $this->printer->text('TEL: ' . $company->telefono1, true, true);
        }

        $this->printer->text($company->cifnif, true, true);
        $this->printer->LineSplitter('=');

        if ($this->headLines) {
            foreach ($this->headLines as $line) {
                $this->printer->text($line, true, true);
            }
        }  
    }

    protected function buildMain()
    {
        $this->printer->text($this->document->codigo, true, true);
        $fechacompleta = $this->document->fecha . ' ' . $this->document->hora;
        $this->printer->text($fechacompleta, true, true);

        $this->printer->text('CLIENTE: ' . $this->document->nombrecliente);
        $this->printer->lineSplitter('=');

        $this->printer->columnText('REFERENCIA', 'CANTIDAD');
        $this->printer->lineSplitter('=');

        //$totaliva = 0.0;
        foreach ($this->document->getLines() as $line) {
            $this->printer->columnText($line->referencia, $line->cantidad);
            $this->printer->text($line->descripcion);

            $this->printer->columnText('PVP:', $line->pvpunitario);
            $this->printer->columnText('IMPORTE:', $line->pvptotal);

            //$totaliva += $line->pvptotal * $line->iva / 100;
        }

        $this->printer->lineSplitter('=');
        $this->printer->columnText('IVA', $this->document->totaliva);
        $this->printer->columnText('TOTAL DEL DOCUMENTO:', $this->document->total);
    }

    protected function buildFoot()
    {
        $this->printer->lineBreak(2);

        if ($this->footLines) {
            foreach ($this->footLines as $line) {
                $this->printer->bigText($line, true, true);
            }
        }

        $this->printer->barcode($this->document->codigo);
    }
}
