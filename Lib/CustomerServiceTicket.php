<?php
namespace FacturaScripts\Plugins\PrintTicket\Lib;

use FacturaScripts\Dinamic\Lib\Ticket\Template\ServiceTemplate;
use FacturaScripts\Dinamic\Model\Empresa;
use FacturaScripts\Dinamic\Model\TicketCustomLine;
use FacturaScripts\Plugins\Servicios\Model\ServicioAT;

class CustomerServiceTicket
{
    private $servicio;
    private $doctype;
    private $template;

    /**
     * SalesDocumentTicket constructor.
     *
     * @param $document
     * @param ServiceTemplate|null $template
     * @param string $doctype identificador del tipo de documento
     * @param int|null $width numero maximo de caracteres por linea.
     */
    public function __construct(ServicioAT $servicio, $width = null, ServiceTemplate $template = null)
    {
        $this->servicio = $servicio;
        $this->doctype = 'Servicio';

        $company = new Empresa();
        $company->loadFromCode($servicio->idempresa);

        $this->template = $template ?: new ServiceTemplate($company, $width);
    }

    public function getTicket()
    {
        return $this->template->buildTicket($this->servicio);
    }

    private function getCustomLines(string $position)
    {
        return TicketCustomLine::rawFromDocument($this->doctype, $position);
    }
}
