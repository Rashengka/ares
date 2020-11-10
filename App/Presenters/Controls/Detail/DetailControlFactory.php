<?php
declare(strict_types = 1);

namespace App\Presenters\Controls\Detail;

use App\Core\Model\Ares\Entities\AresResult;

interface DetailControlFactory
{
    public function create(AresResult $aresResult): DetailControl;
}
