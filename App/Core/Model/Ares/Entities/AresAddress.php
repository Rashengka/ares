<?php
declare(strict_types = 1);

namespace App\Core\Model\Ares\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class AresAddress
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $district;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $cityPart;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $street;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private string $houseNumber;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private ?string $orientationNumber;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private string $zip;

    public function __construct(
        string $district,
        string $city,
        ?string $cityPart,
        ?string $street,
        string $houseNumber,
        ?string $orientationNumber,
        string $zip
    ) {
        $this->id = null;

        $this->district = $district;
        $this->city = $city;
        $this->cityPart = $cityPart;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->orientationNumber = $orientationNumber;
        $this->zip = $zip;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCityPart(): ?string
    {
        return $this->cityPart;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function getOrientationNumber(): ?string
    {
        return $this->orientationNumber;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function toString(bool $district = true, bool $breakLines = true): string
    {
        $bl = $breakLines ? "\n" : ", ";

        $s = ($this->getStreet() ?: $this->getCity()) . " " . $this->getHouseNumber();

        if ($this->getOrientationNumber()) {
            $s .= "/" . $this->getOrientationNumber();
        }
        if ($this->getStreet()) {
            $s .= $bl . $this->getCity();
        }
        if ($this->getCityPart() && $this->getCityPart() != $this->getCity()) {
            $s .= ", " . $this->cityPart;
        }
        $s .= $bl;

        if ($district && $this->getDistrict() && $this->getDistrict() != $this->getCity()) {
            $s .= "okres " . $this->getDistrict() . $bl;
        }

        $s .= $this->getZip();

        return $s;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
