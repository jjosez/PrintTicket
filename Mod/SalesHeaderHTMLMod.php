<?php

namespace FacturaScripts\Plugins\PrintTicket\Mod;

use FacturaScripts\Core\Base\Contract\SalesModInterface;
use FacturaScripts\Core\Base\Translator;
use FacturaScripts\Core\Model\Base\SalesDocument;
use FacturaScripts\Core\Model\User;

class SalesHeaderHTMLMod implements SalesModInterface
{

    public function apply(SalesDocument &$model, array $formData, User $user)
    {
        // TODO: Implement apply() method.
    }

    public function applyBefore(SalesDocument &$model, array $formData, User $user)
    {

    }

    public function assets(): void
    {
        // TODO: Implement assets() method.
    }

    public function newFields(): array
    {
        return [];
    }

    public function renderField(Translator $i18n, SalesDocument $model, string $field): ?string
    {
        if ($field === 'print-ticket') {
            return self::printTicket($i18n, $model);
        }

        return null;
    }

    public function printTicket(Translator $i18n, SalesDocument $model): string
    {
        if ($model->primaryColumnValue()) {
            return '<div class="col-sm-auto">'
                . '<div class="form-group">'
                . '<button class="btn btn-info" type="button" onclick="ticketPrinterAction()">'
                . '<i class="fas fa-print fa-fw"></i> ' . $i18n->trans('print-ticket') . '</button>'
                . '</div>'
                . '</div>';
        }

        return '';
    }

    public function newBtnFields(): array
    {
        return ['print-ticket'];
    }

    public function newModalFields(): array
    {
        return [];
    }
}
