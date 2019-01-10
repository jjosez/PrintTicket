<?php
namespace FacturaScripts\Plugins\PrintTicket\Controller;

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\Base\DataBase;
use FacturaScripts\Dinamic\Lib\TicketBuilder;
use FacturaScripts\Plugins\PrintTicket\Model\Ticket;
use FacturaScripts\Plugins\PrintTicket\Model\TicketCustomLine;

class PrintTicket extends Controller
{
    public $documentType;
    public $documentCode;

    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'Configuracion Tickets';
        $pageData['menu'] = 'admin';
        $pageData['icon'] = 'fas fa-print';
        $pageData['showonmenu'] = FALSE;

        return $pageData;
    }

    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);
        $this->setTemplate('PrintTicketSettings');

        $documentType = $this->request->query->get('documento');
        if ($documentType != '') {
            $this->setTemplate('PrintTicketScreen');
            $this->documentType = $documentType;

            $code = $this->request->query->get('code');
            if ($code != '') {
                
                $className = 'FacturaScripts\\Dinamic\\Model\\' . $documentType;
                $document = (new $className)->get($code);                
                if ($document) {
                    $this->buildTicket($document, $documentType);
                }                
            }
            return;
        }
    }

    private function buildTicket($document, $documentType)
    {
        $width = AppSettings::get('ticket', 'linelength');
        $price = AppSettings::get('ticket', 'printprice');

        switch ($documentType) {
            case 'AlbaranCliente':
                $builder = new TicketBuilder\TicketBuilderAlbaran($width, !$price); 
                break;

            case 'factura':
                $builder = new TicketBuilder\TicketBuilderFactura($width, $price);
                break;
            
            case 'pedido':
                $builder = new TicketBuilder\TicketBuilderPedido($width, $price);
                break;
            
            default:
                # code...
                break;
        }

        if (isset($builder)) {
            $footertext = AppSettings::get('ticket', 'footertext');
            $headerLines = (new TicketCustomLine)->getFromDocument('general', 'header');
            $footerLines = (new TicketCustomLine)->getFromDocument('general', 'footer');

            $builder->setCompany($this->empresa);
            $builder->setDocument($document, $documentType);
            $builder->setCustomHeaderLines($headerLines); 
            $builder->setCustomFooterLines($footerLines);      
            $builder->setFooterText($footertext);

            $ticket = new Ticket();
            $ticket->coddocument = $documentType;
            $ticket->text = $builder->toString();
            $ticket->save();            
        }        
    }
}