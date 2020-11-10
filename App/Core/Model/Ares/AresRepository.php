<?php
declare(strict_types = 1);

namespace App\Core\Model\Ares;

use App\Core\Model\Ares\Entities\AresResult;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

class AresRepository
{
    private EntityManagerInterface $em;

    private ObjectRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(AresResult::class);
    }

    public function save(AresResult $aresResult): void
    {
        $this->em->persist($aresResult);
        $this->em->flush();
    }

    public function findById(int $id): ?AresResult
    {
        return $this->repo->find($id);
    }

    public function getQueryBuilder($alias): QueryBuilder
    {
        return $this->repo->createQueryBuilder($alias);
    }
}
