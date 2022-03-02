<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class Pagination
{
    private string $dql;
    private array $paramsToBind = [];
    private int $currentPage;
    private int $limitPerPage = 9;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function paginate(): Paginator
    {
        $query = $this->entityManager->createQuery($this->dql)
            ->setFirstResult($this->limitPerPage * ($this->currentPage - 1))
            ->setMaxResults($this->limitPerPage);

        if ($this->paramsToBind != null) {
            foreach ($this->paramsToBind['keys'] as $key) {
                foreach ($this->paramsToBind['values'] as $value) {
                    $query->setParameter($key, $value);
                }
            }
        }

        return $paginator = new Paginator($query);
    }

    public function setQuery(string $dql): void
    {
        $this->dql = $dql;
    }

    public function setParamsToBind(array $paramsKeys, array $paramsValues): void
    {
        $this->paramsToBind['keys'] = $paramsKeys;
        $this->paramsToBind['values'] = $paramsValues;
    }

    public function setCurrentPage(Request $request): void
    {
        $this->currentPage = $request->attributes->get('page', 1);
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setLimitPerPage(int $limit): void
    {
        if ($limit > 0 && $limit < 50) {
            $this->limitPerPage = $limit;
        }
    }

    public function getLastPage(Paginator $paginator): int
    {
        return ceil($paginator->count() / $paginator->getQuery()->getMaxResults());
    }
}
