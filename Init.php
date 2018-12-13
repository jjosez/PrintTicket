<?php
namespace FacturaScripts\Plugins\PrintTicket;

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Core\Base\EventManager;
use FacturaScripts\Core\Base\InitClass;
use FacturaScripts\Core\Base\MiniLog;
use FacturaScripts\Core\Model\Empresa;
use FacturaScripts\Core\Model\ApiKey;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;

use FacturaScripts\Plugins\PrintTicket\Model\Ticket;
use FacturaScripts\Dinamic\Lib\TicketBuilder;

class Init extends InitClass
{

    public function init()
    {
        /// código a ejecutar cada vez que carga FacturaScripts (si este plugin está activado).
        EventManager::attach('Model:AlbaranCliente:save', function($albaran) {
		    /// su código aquí
		    /// $model contiene el artículo que se ha eliminado
            $this->buildTicket($albaran, 'albaran');		    
		});
    }

    public function update()
    {
        /// código a ejecutar cada vez que se instala o actualiza el plugin
        $apiKey = new ApiKey();

        if (!$apiKey->loadFromCode('', [new DataBaseWhere('description', 'remoteprinter')])) {
            $apiKey->description = 'remoteprinter';
            $apiKey->nick = 'admin';
            $apiKey->save();
        }
    }

    private function buildTicket($document, $type)
    {
        $log = new MiniLog();
        $logMsg = sprintf("Imprimiendo %s <img src='http://localhost:10080?documento=%s alt='remote-printer'/>", $type, $type); 
		$log->notice($logMsg);

        $width = AppSettings::get('ticket', 'linelength');
        switch ($type) {
            case 'albaran':
                $builder = new TicketBuilder\TicketBuilderAlbaran($width);
                break;

            case 'factura':
                $builder = new TicketBuilderFactura($width);
                break;
            
            case 'pedido':
                $builder = new TicketBuilderPedido($width);
                break;
            
            default:
                # code...
                break;
        }

        if (isset($builder)) {
            $company = (new Empresa)->get(AppSettings::get('default', 'idempresa'));
            $footertext = AppSettings::get('ticket', 'footertext');

            $builder->setCompany($company);
            $builder->setDocument($document, $type);
            //$builder->setCostumHeaderLines($this->headerLines); 
            //$builder->setCostumFooterLines($this->footerLines);      
            $builder->setFooterText($footertext);

            $ticket = new Ticket();
			$ticket->coddocument = $type;
			$ticket->text = $builder->toString();
			$ticket->save();			
        }        
    }
}