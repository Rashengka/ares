<?php
declare(strict_types = 1);

namespace App\Presenters\Controls\Search;

use App\Core\Model\Ares\Entities\AresResult;

interface SearchFormControlFactory
{
    public function create(?AresResult $aresResult): SearchFormControl;
}
