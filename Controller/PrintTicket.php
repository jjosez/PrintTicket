<?php
namespace FacturaScripts\Plugins\PrintTicket\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Dinamic\Lib\TicketPrinter;

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
        $this->setTemplate(false);

        $documentModelName = $this->request->query->get('documento');
        if ($documentModelName != '') {
            $this->setTemplate('PrintTicketScreen');

            $code = $this->request->query->get('code');
            if ($code != '') {
                
                $className = 'FacturaScripts\\Dinamic\\Model\\' . $documentModelName;
                $this->businessDocument = (new $className)->get($code);                
                if ($this->businessDocument) {
                    $this->buildTicket($this->businessDocument);
                }                
            }
            return;
        }
    }

    private function buildTicket($businessDocument)
    {
        $printer = new TicketPrinter();

        if (!$printer->printTicket($businessDocument)) {
            foreach ($printer->getErrors() as $error) {
                echo $error;
            }
        }            
    }
}