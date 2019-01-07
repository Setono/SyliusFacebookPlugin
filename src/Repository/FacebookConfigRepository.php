<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Repository;

use Setono\SyliusFacebookTrackingPlugin\Model\FacebookConfigInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class FacebookConfigRepository extends EntityRepository implements FacebookConfigRepositoryInterface
{
    public function findConfig(): ?FacebookConfigInterface
    {
        return $this->createQueryBuilder('o')
            ->setMaxResults(1)
            ->orderBy('o.id')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
