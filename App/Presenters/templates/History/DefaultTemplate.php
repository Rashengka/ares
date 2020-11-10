<?php
declare(strict_types = 1);

namespace App\Presenters\templates\History;

use App\Core\Model\Ares\Entities\AresResult;

class DefaultTemplate
{
    public bool $showModal;

    public ?AresResult $aresResult;
}