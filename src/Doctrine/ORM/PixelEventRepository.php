<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Doctrine\ORM;

use DateInterval;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;
use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelEventRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Webmozart\Assert\Assert;

class PixelEventRepository extends EntityRepository implements PixelEventRepositoryInterface
{
    public function getCountByPixelAndState(PixelInterface $pixel, string $state): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('count(o)')
            ->andWhere('o.pixel = :pixel')
            ->setParameter('pixel', $pixel)
            ->andWhere('o.state = :state')
            ->setParameter('state', $state)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function hasConsentedPending(int $delay = 0): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->andWhere('o.consentGranted = true')
            ->andWhere('o.state = :state')
            ->setParameter('state', PixelEventInterface::STATE_PENDING, Types::STRING)
        ;

        self::applyDelay($qb, $delay);

        $result = $qb->getQuery()->getSingleScalarResult();

        Assert::integerish($result);

        return (int) $result > 0;
    }

    public function assignBulkIdentifierToPendingConsented(string $bulkIdentifier, int $delay = 0, int $limit = 1000): void
    {
        Assert::greaterThan($limit, 0);

        $qb = $this->createQueryBuilder('o')
            ->update()
            ->set('o.bulkIdentifier', ':bulkIdentifier')
            ->setParameter('bulkIdentifier', $bulkIdentifier, Types::STRING)
            ->andWhere('o.consentGranted = true')
            ->andWhere('o.state = :state')
            ->setParameter('state', PixelEventInterface::STATE_PENDING, Types::STRING)
            ->setMaxResults($limit)
        ;

        self::applyDelay($qb, $delay);

        $qb->getQuery()->execute();
    }

    public function findByBulkIdentifier(string $bulkIdentifier): array
    {
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.bulkIdentifier = :bulkIdentifier')
            ->setParameter('bulkIdentifier', $bulkIdentifier, Types::STRING)
            ->getQuery()
            ->getResult()
        ;

        Assert::isArray($result);
        Assert::allIsInstanceOf($result, PixelEventInterface::class);

        return $result;
    }

    protected static function applyDelay(QueryBuilder $qb, int $delay): void
    {
        Assert::greaterThanEq($delay, 0);

        if ($delay > 0) {
            $then = (new DateTime())->sub(new DateInterval("PT{$delay}S"));
            $qb->andWhere('o.createdAt < :then')
                ->setParameter('then', $then, Types::DATETIME_MUTABLE)
            ;
        }
    }

    public function resetFailedByPixel(PixelInterface $pixel): void
    {
        $this->createQueryBuilder('o')
            ->update()
            ->set('o.state', ':initialState')
            ->setParameter('initialState', PixelEventInterface::STATE_PENDING, Types::STRING)
            ->andWhere('o.pixel = :pixel')
            ->setParameter('pixel', $pixel)
            ->andWhere('o.state = :state')
            ->setParameter('state', PixelEventInterface::STATE_FAILED, Types::STRING)
            ->getQuery()
            ->execute()
        ;
    }

    public function removeSent(int $delay = 0): int
    {
        $qb = $this->_em->createQueryBuilder()
            ->delete($this->_entityName, 'o')
            ->andWhere('o.state = :sentState')
            ->setParameter('sentState', PixelEventInterface::STATE_SENT)
            ;

        self::applyDelay($qb, $delay);

        $result = $qb->getQuery()->execute();
        Assert::integerish($result);

        return (int) $result;
    }
}
