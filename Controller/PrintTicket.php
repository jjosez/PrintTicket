<?php
namespace FacturaScripts\Plugins\PrintTicket\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Dinamic\Lib\BusinessDocumentTicket;
use FacturaScripts\Dinamic\Model\Ticket;

class PrintTicket extends Controller
{
    public $businessDocument;

    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'Configuracion Tickets';
        $pageData['menu'] = 'admin';
        $pageData['icon'] = 'fas fa-print';
        $pageData['showonmenu'] = false;

        return $pageData;
    }

    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);
        $this->setTemplate('PrintTicketScreen');

        $code = $this->request->query->get('code');
        $modelName = $this->request->query->get('documento');        

        if ('' === $modelName || '' === $code) {
            return;
        }
        
        $className = 'FacturaScripts\\Dinamic\\Model\\' . $modelName;
        
        $this->businessDocument = (new $className)->get($code);
        if ($this->businessDocument) {
            $this->savePrintJob($this->businessDocument);
        }
    }

    private function savePrintJob($document)
    {
        $businessTicket = new BusinessDocumentTicket($document); 

        $ticket = new Ticket();
        $ticket->coddocument = $document->modelClassName();
        $ticket->text = $businessTicket->getTicket();  

        //echo $businessTicket->getTicket();   

        if (!$ticket->save()) {
            echo 'Error al guardar el ticket';
        }
    }
}
