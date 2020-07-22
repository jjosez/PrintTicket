<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;

/**
 * 
 */
class DefaultCashupTemplate extends CashupTemplate
{

    public function __construct($width = '50')
    {
        parent::__construct($width);
    }

    protected function buildHead()
    {
        $this->printer->lineBreak();

        $this->printer->lineSplitter('=');
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
        $this->printer->columnText('CIERRE', $this->cashup->getDate());
        $this->printer->lineSplitter('=');

        $this->printer->columnText('SALDO INICIAL', $this->cashup->getInitialAmount());
        $this->printer->lineSplitter();

        $this->printer->text('RESUMEN DE PAGOS', true, true);
        $this->printer->lineBreak();

        foreach ($this->cashup->getPayments() as $payment) {
            $this->printer->columnText(strtoupper($payment->getMethod()), $payment->getAmount());
        }

        $this->printer->lineSplitter('=');
        $this->printer->columnText('TOTAL ESPERADO', $this->cashup->getSpectedTotal());
        $this->printer->columnText('TOTAL CONTADO', $this->cashup->getTotal());
    }

    public function buildTicket(Cashup $cashup, Company $company, bool $cut = true, bool $open = true): string
    {
        $this->company = $company;
        $this->cashup = $cashup;

        $this->buildHead();
        $this->buildMain();

        $this->printer->lineBreak();
        $this->openDrawerCommand($open);
        $this->cutPapperCommand($cut);

        return $this->printer->output();
    }
}
