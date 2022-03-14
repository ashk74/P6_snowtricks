<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Manage pagination system
 */
class Pagination
{
    private string $dql;
    private array $paramsToBind = [];
    private int $lastPage;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Manage pagination using Doctrine Paginator
     *
     * @param integer $page
     * @param integer $limit
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function paginate(int $page, int $limit): Paginator
    {
        // Use DQL for data queries
        $query = $this->entityManager->createQuery($this->dql)
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        // If DQL contain params to bind add $query->setParameter
        if ($this->paramsToBind != null) {
            foreach ($this->paramsToBind['keys'] as $key) {
                foreach ($this->paramsToBind['values'] as $value) {
                    $query->setParameter($key, $value);
                }
            }
        }

        // Initialize Doctrine Paginator
        $paginator = new Paginator($query);

        // Set the last page
        $this->setLastPage($paginator->count(), $limit);

        return $paginator;
    }

    private function setLastPage(int $totalItems, int $limit)
    {
        $this->lastPage = ceil($totalItems / $limit);
    }

    public function getLastPage()
    {
        return $this->lastPage;
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
}
