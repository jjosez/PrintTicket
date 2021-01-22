<?php

namespace FacturaScripts\Plugins\PrintTicket\Lib\Ticket\Template;

use FacturaScripts\Dinamic\Model\Empresa;
use FacturaScripts\Plugins\Servicios\Model\ServicioAT;

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
        $this->printer->lineBreak();

        $this->printer->lineSplitter();
        $this->printer->text($this->empresa->nombrecorto, true, true);
        $this->printer->bigText($this->empresa->direccion, true, true);

        if ($this->empresa->telefono1) {
            $this->printer->text('TEL: ' . $this->empresa->telefono1, true, true);
        }

        $this->printer->text($this->empresa->cifnif, true, true);
        $this->printer->LineSplitter('=');
    }

    protected function buildMain()
    {
        $this->printer->keyValueText('Servicio No.', $this->servicio->idservicio);
        $this->printer->keyValueText('Cliente ', $this->servicio->codcliente);
        $this->printer->keyValueText('Fecha', $this->servicio->fecha);
        $this->printer->keyValueText('Hora', $this->servicio->hora);
        $this->printer->lineSplitter('=');

        $this->printer->text('Descripcion: ');
        $this->printer->bigText($this->servicio->descripcion);
        $this->printer->lineBreak();

        $this->printer->text('Observaciones: ');
        $this->printer->bigText($this->servicio->observaciones);
        $this->printer->lineSplitter('=');

        $this->buildServiceJobs();
        $this->printer->lineSplitter('=');
    }

    protected function buildServiceJobs()
    {
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
            $this->printer->lineSplitter();
        }
    }

    public function buildTicket(ServicioAT $servicio) : string
    {
        $this->servicio = $servicio;

        $this->buildHead();
        $this->buildMain();
        $this->buildFoot();

        $this->printer->lineBreak(2);

        return $this->printer->output();
    }
}