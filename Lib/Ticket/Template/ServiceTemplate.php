<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use _HumbugBox3ab8cff0fda0\___PHPSTORM_HELPERS\this;
use FacturaScripts\Core\Base\DivisaTools;
use FacturaScripts\Core\Model\Base\BusinessDocument;
use FacturaScripts\Dinamic\Model\Empresa;
use FacturaScripts\Plugins\Servicios\Model\ServicioAT;
use FacturaScripts\Plugins\Servicios\Model\TrabajoAT;

/**
 *
 */
class ServiceTemplate extends BaseTicketTemplate
{
    protected $servicio;

    public function __construct(Empresa $empresa, $width)
    {
        parent::__construct($empresa, $width);
    }

    protected function buildFoot()
    {
        $this->printer->lineBreak(2);

        $this->printer->barcode($this->servicio->idservicio);
    }

    protected function buildHead()
    {
        $company = $this->empresa;
        $this->printer->lineBreak();

        $this->printer->lineSplitter();
        $this->printer->text($company->nombrecorto, true, true);
        $this->printer->bigText($company->direccion, true, true);

        if ($company->telefono1) {
            $this->printer->text('TEL: ' . $company->telefono1, true, true);
        }

        $this->printer->text($company->cifnif, true, true);
        $this->printer->LineSplitter('=');
    }

    protected function buildMain()
    {
        $this->printer->text($this->servicio->idservicio, true, true);
        $this->printer->keyValueText('Fecha', $this->servicio->fecha);
        $this->printer->keyValueText('Hora', $this->servicio->hora);

        $this->printer->text('Cliente: ' . $this->servicio->codcliente);
        $this->printer->lineSplitter('=');

        $this->printer->lineBreak();
        $this->printer->text('Descripcion: ');
        $this->printer->bigText($this->servicio->descripcion);

        $this->printer->lineBreak();
        $this->printer->text('Observaciones: ');
        $this->printer->bigText($this->servicio->observaciones);
        $this->printer->lineSplitter('=');

        $this->buildTrabajos();
        $this->printer->lineSplitter('=');
    }

    protected function buildTrabajos()
    {
        $this->printer->lineBreak();
        $this->printer->text('TRABAJOS', true, true);
        $this->printer->lineSplitter();

        foreach ($this->servicio->getTrabajos() as $trabajo) {
            $this->printer->keyValueText('Inicio:', $trabajo->fechainicio . ' ' . $trabajo->horainicio);
            $this->printer->keyValueText('Hasta:', $trabajo->fechafin . ' ' . $trabajo->horafin);

            $this->printer->lineBreak();
            $this->printer->text('Observaciones: ');
            $this->printer->bigText($trabajo->observaciones);

            $this->printer->lineBreak();
            $this->printer->text('Descripcion: ');
            $this->printer->bigText($trabajo->descripcion);
        }
    }

    public function buildTicket(ServicioAT $servicio, bool $cut = true, bool $open = true) : string
    {
        $this->servicio = $servicio;

        $this->buildHead();
        $this->buildMain();
        $this->buildFoot();

        $this->printer->lineBreak();
        $this->openDrawerCommand($open);
        $this->cutPapperCommand($cut);

        return $this->printer->output();
    }
}