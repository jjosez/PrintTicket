<?php
namespace FacturaScripts\Plugins\PrintTicket\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Core\App\AppSettings;

class PrintTicketSettings extends Controller
{
    public $documentType;
    public $documentCode;

    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'Configuracion Tickets';
        $pageData['menu'] = 'admin';
        $pageData['icon'] = 'fas fa-print';

        return $pageData;
    }

    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);
        $this->setTemplate('PrintTicketSettings');
        $appSettings = new AppSettings();

        $documento = $this->request->query->get('documento');
        if ($documento != '') {
            $this->setTemplate('PrintTicketScreen');
            $this->documentType = $documento;
        }

        $footerText = $this->request->request->get('footertext');
        if ($footerText) {
            //$coddivisa = AppSettings::get('default', 'coddivisa');
            $appSettings->set('ticket', 'footertext', $footerText);
        }

        $lineLength = $this->request->request->get('linelength');
        if ($lineLength) {
            $appSettings->set('ticket', 'linelength', $lineLength);
        }
        $appSettings->save();
    }
}