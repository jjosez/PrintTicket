<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use _HumbugBox3ab8cff0fda0\___PHPSTORM_HELPERS\this;
use FacturaScripts\Core\Model\Base\BusinessDocument;
use FacturaScripts\Dinamic\Model\Empresa;

/**
 *
 */
class SalesTemplate extends BaseTicketTemplate
{
    protected $document;
    protected $headLines;
    protected $footLines;

    public function __construct(Empresa $empresa, $width)
    {
        parent::__construct($empresa, $width);

        $this->headLines = [];
        $this->footLines = [];
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

        $this->printer->columnText('CANTIDAD', 'ARTICULO');
        $this->printer->lineSplitter('=');

        //$totaliva = 0.0;
        foreach ($this->document->getLines() as $line) {
            $producto = $line->cantidad . ' x ' . $line->referencia . ' - ' . $line->descripcion;
            //$this->printer->columnText($producto, $line->pvpunitario);
            $this->printer->text($producto);
            //$this->printer->columnText($line->cantidad, $producto);

            $this->printer->columnText('P. Unitario:', $line->pvpunitario);
            $this->printer->columnText('Impuesto:', $line->iva);

            $descuento = $line->pvpunitario - ($line->pvpunitario * $line->getEUDiscount());
            $this->printer->columnText('Descuento:', $descuento);
            $this->printer->columnText('Importe:', $line->pvpsindto);
            $this->printer->columnText('Importe:', $line->pvptotal);
            $this->printer->lineBreak();

            //$totaliva += $line->pvptotal * $line->iva / 100;
        }

        $this->printer->lineSplitter('=');
        $this->printer->columnText('IVA', $this->document->totaliva);
        $this->printer->columnText('TOTAL DEL DOCUMENTO:', $this->document->total);
    }

    public function buildTicket(BusinessDocument $document, array $headlines, array $footlines, bool $cut = true, bool $open = true) : string
    {
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
}
