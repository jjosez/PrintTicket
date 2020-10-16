<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;


use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Model\Empresa;

/**
 * 
 */
class DefaultCashupTemplate extends CashupTemplate
{

    public function __construct(Empresa $empresa, int $width)
    {
        parent::__construct($empresa, $width);
    }

    protected function buildHead()
    {
        $this->printer->lineBreak();

        $this->printer->lineSplitter('=');
        $this->printer->text($this->empresa->nombrecorto, true, true);
        $this->printer->bigText($this->empresa->direccion, true, true);

        if ($this->empresa->telefono1) {
            $this->printer->text('TEL: ' . $this->empresa->telefono1, true, true);
        }
        if ($this->empresa->telefono2) {
            $this->printer->text('TEL: ' . $this->empresa->telefono2, true, true);
        }

        $this->printer->text($this->empresa->cifnif, true, true);
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

    public function buildTicket(Cashup $cashup, bool $cut = true, bool $open = true): string
    {
        $this->cashup = $cashup;

        $this->buildHead();
        $this->buildMain();

        $this->printer->lineBreak();
        $this->openDrawerCommand($open);
        $this->cutPapperCommand($cut);

        return $this->printer->output();
    }
}
