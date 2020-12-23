<?php
namespace FacturaScripts\Plugins\PrintTicket;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Base\InitClass;
use FacturaScripts\Core\Lib\AssetManager;
use FacturaScripts\Core\Model\ApiKey;


class Init extends InitClass
{

    public function init()
    {
        //AssetManager::add('js', FS_ROUTE . '/Dinamic/Assets/JS/Print.js');
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
}