<?php
declare(strict_types = 1);

namespace App\Core\Model\Ares\Entities;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity()
 */
class AresResult
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private ?int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $dateInsert;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private string $searchedId;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTimeInterface $dateIdCreate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTimeInterface $dateIdValid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $companyName;

    /**
     * @ORM\OneToOne(targetEntity="\App\Core\Model\Ares\Entities\AresAddress", orphanRemoval=true, cascade={"all"})
     * @ORM\JoinColumn(nullable=true)
     */
    private ?AresAddress $companyAddress;

    public function __construct(
        string $searchedId,
        DateTimeInterface $dateIdCreate,
        DateTimeInterface $dateIdValid,
        string $companyName,
        ?AresAddress $companyAddress
    ) {
        $this->id = null;
        $this->dateInsert = DateTime::from("NOW");

        $this->searchedId = $searchedId;
        $this->dateIdCreate = $dateIdCreate;
        $this->dateIdValid = $dateIdValid;
        $this->companyName = $companyName;
        $this->companyAddress = $companyAddress;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInsert(): DateTimeInterface
    {
        return clone $this->dateInsert;
    }

    public function getSearchedId(): string
    {
        return $this->searchedId;
    }

    public function getDateIdCreate(): DateTimeInterface
    {
        return $this->dateIdCreate;
    }

    public function getDateIdValid(): ?DateTimeInterface
    {
        return $this->dateIdValid;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getCompanyAddress(): ?AresAddress
    {
        return $this->companyAddress;
    }
}
