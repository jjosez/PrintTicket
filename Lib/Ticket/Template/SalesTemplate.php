<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Core\Base\DivisaTools;
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

        $this->printer->text('ARTICULO');
        $columnas = $this->printer->columnText(3, 'CANTIDAD');
        $columnas .= $this->printer->columnText(3, 'UNITARIO');
        $columnas .= $this->printer->columnText(3, 'IMPORTE');
        $this->printer->text($columnas);
        $this->printer->lineSplitter('=');

        $divisaTool = new DivisaTools();
        $divisaTool->findDivisa($this->document);

        foreach ($this->document->getLines() as $line) {
            $this->printer->text("$line->referencia - $line->descripcion");
            $desglose = $this->printer->columnText(3, $line->cantidad);
            $desglose .= $this->printer->columnText(3, $divisaTool::format($line->pvpunitario));
            $desglose .= $this->printer->columnText(3, $divisaTool::format($line->pvpsindto));
            $this->printer->text($desglose);

            $descuento = $line->pvpsindto - ($line->pvpsindto * $line->getEUDiscount());
            $this->printer->keyValueText('Descuento:', '- ' . $divisaTool::format($descuento));

            $impuestoLinea = $line->pvptotal * $line->iva / 100;
            $this->printer->keyValueText("Impuesto $line->iva%:", '+ ' . $divisaTool::format($impuestoLinea));
            $this->printer->keyValueText('Total linea:', $divisaTool::format($line->pvptotal + $impuestoLinea));

            $this->printer->lineBreak();
        }

        $this->printer->lineSplitter('=');
        $this->printer->keyValueText('BASE', $divisaTool::format($this->document->neto));
        $this->printer->keyValueText('IVA', $divisaTool::format($this->document->totaliva));
        $this->printer->keyValueText('TOTAL DEL DOCUMENTO:', $divisaTool::format($this->document->total));
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
