<?php
declare(strict_types = 1);

namespace App\Presenters;

use App\Core\Model\Ares\AresFacade;
use App\Core\Model\Ares\Entities\AresResult;
use App\Presenters\Controls\Detail\DetailControl;
use App\Presenters\Controls\Detail\DetailControlFactory;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

class HistoryPresenter extends Presenter
{
    private ?AresResult $aresResult;

    private bool $showModal;

    private AresFacade $facade;

    private DetailControlFactory $detailControlFactory;

    public function __construct(AresFacade $facade, DetailControlFactory $detailControlFactory)
    {
        parent::__construct();
        $this->aresResult = null;

        $this->facade = $facade;
        $this->showModal = false;
        $this->detailControlFactory = $detailControlFactory;
    }

    protected function createComponentGrid(): DataGrid
    {
        $grid = new DataGrid();
        $grid->setDataSource($this->facade->getGridDataSource());
        $grid->setRememberState(false);
        $grid->setDefaultSort(["dateInsert" => "DESC"], false);
        $grid->setItemsPerPageList([3, 10, 25], true);
        $grid->setDefaultPerPage(3);

        $grid->addColumnDateTime("dateInsert", "Datum hledání")
            ->setAlign("left")
            ->setFormat("j.n.Y, H:i:s")
            ->setSortable()
            ->setSortableResetPagination()
            ->setFilterDateRange();

        $grid->addColumnText("searchedId", "IČO")
            ->setSortable()
            ->setSortableResetPagination()
            ->setFilterText();

        $grid->addColumnText("companyName", "Firma")
            ->setSortable()
            ->setSortableResetPagination()
            ->setFilterText();

        $grid->addAction("show!", "Zobrazit detail", null, [
            "id" => "id",
        ])->setClass("btn btn-xs btn-default btn-secondary show-modal");

        $translator = new SimpleTranslator([
            'ublaboo_datagrid.no_item_found_reset' => 'Žádné položky nenalezeny. Filtr můžete vynulovat',
            'ublaboo_datagrid.no_item_found' => 'Žádné položky nenalezeny.',
            'ublaboo_datagrid.here' => 'zde',
            'ublaboo_datagrid.items' => 'Položky',
            'ublaboo_datagrid.all' => 'všechny',
            'ublaboo_datagrid.from' => 'z',
            'ublaboo_datagrid.reset_filter' => 'Resetovat filtr',
            'ublaboo_datagrid.group_actions' => 'Hromadné akce',
            'ublaboo_datagrid.show_all_columns' => 'Zobrazit všechny sloupce',
            'ublaboo_datagrid.hide_column' => 'Skrýt sloupec',
            'ublaboo_datagrid.action' => 'Akce',
            'ublaboo_datagrid.previous' => 'Předchozí',
            'ublaboo_datagrid.next' => 'Další',
            'ublaboo_datagrid.choose' => 'Vyberte',
            'ublaboo_datagrid.execute' => 'Provést',
        ]);

        $grid->setTranslator($translator);

        return $grid;
    }

    protected function createComponentDetail(): DetailControl
    {
        return $this->detailControlFactory->create($this->aresResult);
    }

    public function handleShow(int $id): void
    {
        $this->aresResult = $this->facade->findById($id);
        $this->showModal = true;
        if ($this->isAjax()) {
            $this->getPayload()->showModal = true;
            $this->redrawControl("resultDetail");
        }
    }

    public function renderDefault()
    {
        $this->template->setParameters([
            "showModal" => $this->showModal,
            "aresResult" => $this->aresResult,
        ]);
    }
}
