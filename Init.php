<?php
namespace FacturaScripts\Plugins\PrintTicket;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Base\InitClass;
use FacturaScripts\Dinamic\Model\ApiKey;
use FacturaScripts\Dinamic\Model\ApiAccess;

class Init extends InitClass
{

    public function init()
    {
        $this->loadExtension(new Extension\Controller\EditAlbaranCliente());
        $this->loadExtension(new Extension\Controller\EditFacturaCliente());
        $this->loadExtension(new Extension\Controller\EditPedidoCliente());
        $this->loadExtension(new Extension\Controller\EditPresupuestoCliente());
    }

    public function update()
    {
        $apiKey = new ApiKey();
        $where = [new DataBaseWhere('description', 'remoteprinter')];

        if (false === $apiKey->loadFromCode('', $where)) {
            $apiKey->description = 'remoteprinter';
            $apiKey->nick = 'admin';
            $apiKey->save();
        }

        $apiAcces = new ApiAccess();
        $where = [
            new DataBaseWhere('idapikey', $apiKey->id),
            new DataBaseWhere('resource', 'ticketes')
        ];

        if (false === $apiAcces->loadFromCode('', $where)) {
            $apiAcces->resource = 'ticketes';
            $apiAcces->idapikey = $apiKey->id;
            $apiAcces->allowget = true;
            $apiAcces->allowdelete = true;
            $apiAcces->save();
        }
    }
}
