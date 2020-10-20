<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Lib\Ticket\Template\CashupTemplate;
use FacturaScripts\Dinamic\Model\Empresa;

class CashupTicket
{
    private $session;
    private $template;

    public function __construct($session, Empresa $empresa, $width = null, CashupTemplate $template = null)
    {
        $this->session = $session;
        $this->template = $template ?: new CashupTemplate($empresa, $width);
    }

    public function getTicket()
    {
        $cashup = new Cashup(
            $this->session->idsesion,
            $this->session->saldoinicial,
            $this->session->saldoesperado,
            $this->session->saldocontado,
            null
        );

        foreach ($this->session->getOperaciones() as $oper) {
            $cashup->addOperation(
                'Operacion: ' . $oper->idoperacion,
                $oper->tipodoc . ' ' . $oper->codigo,
                $oper->total
            );
        }

        return $this->template->buildTicket($cashup);
    }
}
