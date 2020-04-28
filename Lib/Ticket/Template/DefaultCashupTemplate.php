<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Customer;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Document;
use FacturaScripts\Dinamic\Lib\Ticket\Template\AbstractTemplate;

/**
 * 
 */
class DefaultCashupTemplate extends AbstractTemplate
{
    protected $cashup;

    function __construct($width = '50')
    {
        parent::__construct($width);
    }

    public function buildDocumentTicket(
        Document $document, 
        Customer $customer, 
        Company $company, 
        array $headlines, 
        array $footlines,
        bool $cut,
        bool $open
    ) : string {

        return '';
    }

    public function buildCashupTicket(
        Cashup $cashup, 
        Company $company, 
        bool $cut, 
        bool $open
    ) : string {
        $this->company = $company;
        $this->cashup = $cashup;

        ($open) ? $this->printer->open() : null;

        $this->buildHead();
        $this->buildMain();

        ($cut) ? $this->printer->cut() : null;

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
    }

    protected function buildMain()
    {
        $this->printer->text($this->cashup->getDate(), true, true);
        $this->printer->lineSplitter('=');

        $this->printer->columnText('DOCUMENTO','TOTAL');
        $this->printer->lineSplitter('=');

        foreach ($this->cashup->getOperations() as $operation) {
            $this->printer->text($operation->getId(), true);
            $this->printer->columnText($operation->getCode(), $operation->getAmount());
        }

        $this->printer->columnText('SALDO INICIAL', $this->cashup->getInitialAmount());

        $this->printer->lineSplitter('=');          
        $this->printer->columnText('TOTAL ESPERADO', $this->cashup->getSpectedTotal());
        $this->printer->columnText('TOTAL CONTADO:', $this->cashup->getTotal());
    }
}
