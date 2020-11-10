<?php
declare(strict_types = 1);

namespace App\Presenters\Controls\Detail;

use App\Core\Model\Ares\Entities\AresResult;
use Nette\Application\UI\Control;

class DetailControl extends Control
{

    /**
     * @var AresResult
     */
    private AresResult $aresResult;

    public function __construct(AresResult $aresResult)
    {
        $this->aresResult = $aresResult;
    }

    public function render(): void
    {
        $this->template->render(__DIR__ . "/template.latte", [
            "aresResult" => $this->aresResult,
        ]);
    }
}
