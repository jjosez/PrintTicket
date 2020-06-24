<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use DateTime;
use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Cashup;
use FacturaScripts\Dinamic\Lib\Ticket\Data\Company;
use FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template\DefaultCashupTemplate;

class CashupTicket
{
    private $company;
    private $session;
    private $width;

    function __construct($session, $company, float $width = null)
    {
        $this->company = $company;
        $this->session = $session;
        $this->width = (empty($width)) ? $this->getDefaultWitdh() : $width;
    }

    public function getTicket()
    {
        $company = new Company(
            $this->company->nombrecorto,
            $this->company->cifnif,
            $this->company->direccion
        );

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

        $template = new DefaultCashupTemplate($this->width);

        $builder = new Ticket\TicketBuilder($company, $template);
        return $builder->buildFromCashup($cashup);
    }

    private function getDefaultWitdh()
    {
        return AppSettings::get('ticket', 'linelength', 50);
    }
}
