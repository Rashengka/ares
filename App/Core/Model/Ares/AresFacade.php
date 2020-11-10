<?php
declare(strict_types = 1);

namespace App\Core\Model\Ares;

use App\Core\Model\Ares\Entities\AresResult;
use Doctrine\ORM\QueryBuilder;

class AresFacade
{
    private AresClient $client;

    /**
     * @var AresRepository
     */
    private AresRepository $repo;

    public function __construct(AresClient $client, AresRepository $repo)
    {
        $this->client = $client;
        $this->repo = $repo;
    }

    /**
     * @throws Exceptions\AresClientException
     */
    public function search(string $searchedId): ?AresResult
    {
        $searchResult = $this->client->search($searchedId);
        if (null === $searchResult) {
            return null;
        }
        $this->repo->save($searchResult);

        return $searchResult;
    }

    public function getGridDataSource(): QueryBuilder
    {
        return $this->repo->getQueryBuilder("a");
    }

    public function findById(int $id)
    {
        return $this->repo->findById($id);
    }
}