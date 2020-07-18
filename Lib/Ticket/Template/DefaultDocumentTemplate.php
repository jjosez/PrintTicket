<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Customer;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Document;

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
        Document $document, 
        Customer $customer, 
        Company $company, 
        array $headlines, 
        array $footlines,
        bool $cut = true,
        bool $open = true
    ) : string {
        $this->company = $company;
        $this->customer = $customer;
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
        $this->printer->lineBreak();

        $this->printer->lineSplitter();
        $this->printer->text($this->company->getName(), true, true);
        $this->printer->bigText($this->company->getAddress(), true, true);

        if ($this->company->getPhone()) {
            $this->printer->text('TEL: ' . $this->company->getPhone(), true, true);
        }

        $this->printer->text($this->company->getVatID(), true, true);
        $this->printer->LineSplitter('=');

        if ($this->headLines) {
            foreach ($this->headLines as $line) {
                $this->printer->text($line, true, true);
            }
        }  
    }

    protected function buildMain()
    {
        $this->printer->text($this->document->getCode(), true, true);
        $this->printer->text($this->document->getDate(), true, true);

        $this->printer->text('CLIENTE: ' . $this->customer->getName());
        $this->printer->lineSplitter('=');

        $this->printer->columnText('REFERENCIA','CANTIDAD');
        $this->printer->lineSplitter('=');

        $totaliva = 0.0;
        foreach ($this->document->getLines() as $line) {
            $this->printer->columnText($line->getCode(), $line->getQuantity());
            $this->printer->text($line->getDescription());

            $this->printer->columnText('PVP:', $line->getPrice());
            $this->printer->columnText('IMPORTE:', $line->getTotal());

            $totaliva += $line->getTotal() * $line->getTax() / 100;    
        }

        $this->printer->lineSplitter('=');          
        $this->printer->columnText('IVA', $totaliva);
        $this->printer->columnText('TOTAL DEL DOCUMENTO:', $this->document->getTotal());
    }

    protected function buildFoot()
    {
        $this->printer->lineBreak(2);

        if ($this->footLines) {
            foreach ($this->footLines as $line) {
                $this->printer->bigText($line, true, true);
            }
        }

        $this->printer->barcode($this->document->getCode());
    }
}
