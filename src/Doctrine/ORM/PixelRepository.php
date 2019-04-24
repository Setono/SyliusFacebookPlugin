<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Doctrine\ORM;

use Setono\SyliusFacebookTrackingPlugin\Repository\PixelRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;

class PixelRepository extends EntityRepository implements PixelRepositoryInterface
{
    public function findEnabledByChannel(ChannelInterface $channel): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
        ;
    }
}
