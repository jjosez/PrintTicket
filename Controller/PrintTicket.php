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
        $appSettings = new AppSettings();

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

        $footerText = $this->request->request->get('footertext');
        if ($footerText) {
            $appSettings->set('ticket', 'footertext', $footerText);
        }

        $lineLength = $this->request->request->get('linelength');
        if ($lineLength) {
            $appSettings->set('ticket', 'linelength', $lineLength);
        }
        $appSettings->save();
    }

    private function buildTicket($document, $documentType)
    {
        $width = AppSettings::get('ticket', 'linelength');

        switch ($documentType) {
            case 'AlbaranCliente':
                $builder = new TicketBuilder\TicketBuilderAlbaran($width); 
                break;

            case 'factura':
                $builder = new TicketBuilder\TicketBuilderFactura($width);
                break;
            
            case 'pedido':
                $builder = new TicketBuilder\TicketBuilderPedido($width);
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