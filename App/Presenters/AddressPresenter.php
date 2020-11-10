<?php
declare(strict_types = 1);

namespace App\Presenters;

use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Presenter;

class AddressPresenter extends Presenter
{
    /**
     * @persistent
     */
    public int $addressId;

    private ?array $addressRow;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function startup()
    {
        parent::startup();

        $this->addressRow = $this->getAddressRow();
        if (null === $this->addressRow) {
            $this->error("Address #$this->addressId not found!");
        }
    }

    public function renderDefault(): void
    {
        $this->template->setParameters([
            "address" => $this->addressRow,
        ]);
    }

    private function getAddressRow(): ?array
    {
        $sql = <<<SQL
SELECT * FROM address WHERE address_id = ?
SQL;

        $row = $this->em->getConnection()->executeQuery(
            $sql,
            [$this->addressId]
        )->fetchAssociative();

        if (!$row) {
            return null;
        }

        return $row;
    }
}