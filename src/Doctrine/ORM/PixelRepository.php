<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Doctrine\ORM;

use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Webmozart\Assert\Assert;

class PixelRepository extends EntityRepository implements PixelRepositoryInterface
{
    public function findEnabledByChannel(ChannelInterface $channel): array
    {
        $result = $this->createQueryBuilder('o')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
        ;

        Assert::isArray($result);
        Assert::allIsInstanceOf($result, PixelInterface::class);

        return $result;
    }
}
