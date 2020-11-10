<?php
declare(strict_types = 1);

namespace App\Core\Model\Ares;

use App\Core\Model\Ares\Entities\AresAddress;
use App\Core\Model\Ares\Entities\AresResult;
use App\Core\Model\Ares\Exceptions\AresClientException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use LaLit\XML2Array;
use Nette\Utils\DateTime;

class AresClient
{
    private const ARES_URI = "http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_std.cgi";

    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            "base_uri" => self::ARES_URI,
        ]);
    }

    /**
     * @return AresResult|null
     * @throws AresClientException
     */
    public function search(string $searched): ?AresResult
    {
        try {
            $result = $this->client->get("?ico=$searched");
        } catch (GuzzleException $e) {
            throw new AresClientException("Nepodařilo se získat data z databáze ARES!", $e->getCode(), $e);
        }

        if ($result->getStatusCode() != 200) {
            throw new AresClientException("Nepodařilo se úspěšně získat data z databáze ARES!");
        }

        $bodyContents = $result->getBody()->getContents();
        if (empty($bodyContents)) {
            throw new AresClientException(
                "Nepodařilo se úspěšně získat data z databáze ARES! Vrátila se prázdná odpověď."
            );
        }

        // Life hack :D
        try {
            $data = XML2Array::createArray($bodyContents);
        } catch (Exception $e) {
            throw new AresClientException("Nepodařilo se přečíst odpověď z databáze ARES!", 500);
        }

        $odpoved = $data["are:Ares_odpovedi"]["are:Odpoved"] ?? null;
        if (null == $odpoved) {
            throw new AresClientException("Neplatná odpověď z databáze ARES!");
        }
        if (isset($odpoved["are:Error"])) {
            throw new AresClientException(
                $odpoved["are:Error"]["dtt:Error_text"] ?? "Neznámá chyba",
                isset($odpoved["are:Error"]["dtt:Error_kod"]) && is_numeric($odpoved["are:Error"]["dtt:Error_kod"]) ?
                    (int)$odpoved["are:Error"]["dtt:Error_kod"] :
                    null
            );
        }
        $zaznam = $odpoved["are:Zaznam"] ?? null;
        if (null === $zaznam) {
            return null;
        }

        $adresa = $zaznam["are:Identifikace"]["are:Adresa_ARES"] ?? null;

        if (null !== $adresa) {
            $address = new AresAddress(
                (string)$adresa["dtt:Nazev_okresu"] ?? "",
                (string)$adresa["dtt:Nazev_obce"] ?? "",
                ($adresa["dtt:Nazev_casti_obce"] ?? null) ?: null,
                ($adresa["dtt:Nazev_ulice"] ?? null) ?: null,
                (string)$adresa["dtt:Cislo_domovni"] ?? "",
                ($adresa["dtt:Cislo_orientacni"] ?? null) ?: null,
                (string)$adresa["dtt:PSC"]
            );
        } else {
            $address = null;
        }

        return new AresResult(
            $searched,
            DateTime::from($zaznam["are:Datum_vzniku"]),
            DateTime::from($zaznam["are:Datum_platnosti"]),
            $zaznam["are:Obchodni_firma"] ?? "???",
            $address
        );
    }
}