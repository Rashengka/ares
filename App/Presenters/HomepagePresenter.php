<?php
declare(strict_types = 1);

namespace App\Presenters;

use App\Core\Model\Ares\AresRepository;
use App\Core\Model\Ares\Entities\AresResult;
use App\Presenters\Controls\Detail\DetailControl;
use App\Presenters\Controls\Detail\DetailControlFactory;
use App\Presenters\Controls\Search\SearchFormControl;
use App\Presenters\Controls\Search\SearchFormControlFactory;
use App\Presenters\templates\Layouts;
use Nette\Application\UI\Presenter;

final class HomepagePresenter extends Presenter
{
    private SearchFormControlFactory $aresFormControlFactory;

    private AresRepository $aresRepository;

    private ?AresResult $aresResult;

    private bool $searched;

    /**
     * @var DetailControlFactory
     */
    private DetailControlFactory $detailControlFactory;

    public function __construct(
        SearchFormControlFactory $searchFormControlFactory,
        AresRepository $aresRepository,
        DetailControlFactory $detailControlFactory
    ) {
        parent::__construct();
        $this->aresFormControlFactory = $searchFormControlFactory;
        $this->aresRepository = $aresRepository;

        $this->aresResult = null;
        $this->searched = false;
        $this->detailControlFactory = $detailControlFactory;
    }

    protected function beforeRender()
    {
        parent::beforeRender();
        $this->setLayout(Layouts::LAYOUT);
    }

    protected function createComponentSearch(): SearchFormControl
    {
        $ares = $this->aresFormControlFactory->create($this->aresResult);
        $ares->onSuccess[] = function (?AresResult $aresResult): void {
            $this->aresResult = $aresResult;
            $this->searched = true;
            if ($this->isAjax()) {
                $this->redrawControl("searchResult");
            } else {
                $this->redirect("this", [
                    "id" => $aresResult->getId(),
                ]);
            }
        };

        return $ares;
    }

    protected function createComponentDetail(): DetailControl
    {
        return $this->detailControlFactory->create($this->aresResult);
    }

    public function actionDefault(?int $id)
    {
        if (null !== $id) {
            $this->aresResult = $this->aresRepository->findById($id);
        }
    }

    public function renderDefault()
    {
        $this->template->setParameters([
            "aresResult" => $this->aresResult,
            "searched" => $this->searched,
        ]);
    }

    public function flashSuccess(string $message): void
    {
        $this->flashMessage($message, "success");
        $this->redrawFlashes();
    }

    public function flashError(string $message): void
    {
        $this->flashMessage($message, "danger");
        $this->redrawFlashes();
    }

    private function redrawFlashes(): void
    {
        $this->redrawControl("flashes");
    }
}
