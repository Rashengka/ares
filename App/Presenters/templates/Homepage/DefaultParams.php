<?php
declare(strict_types = 1);

namespace App\Presenters\templates\Homepage;

use App\Core\Model\Ares\Entities\AresResult;

class DefaultParams
{
    public ?AresResult $aresResult;

    public bool $searched;
}
