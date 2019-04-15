<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Lib\Ticket\Data;
/**
 * 
 */
class DefaultTemplate extends TicketTemplate
{
    private $cashup;
    private $customer;
    private $document;
    private $headLines;
    private $footLines;

    function __construct($width)
    {
        parent::__construct($width);

        $this->headLines = [];
        $this->footlines = [];
    }

    public function buildDocumentTicket(
        Data\Document $document, 
        Data\Customer $customer, 
        Data\Company $company, 
        array $headlines, 
        array $footlines
    ) : string
    {
        $this->company = $company;
        $this->customer = $customer;
        $this->document = $document;
        $this->headLines = $headlines;
        $this->footLines = $footlines;


        $this->buildHead();

        return $this->printer->output();
    }

    public function buildCashupTicket(Cashup $cashup, Company $company) : string
    {
        $this->company = $company;
        $this->cashup = $cashup;

        $this->buildHead();

        return $this->printer->output();
    }

    private function buildHead()
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

    private function buildMain()
    {

    }

    private function buildFoot()
    {

    }
}
