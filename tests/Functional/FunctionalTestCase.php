<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelRepositoryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\Model\CodeAwareInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Every test runs inside a database transaction that is rolled back afterwards (see dama/doctrine-test-bundle),
 * so tests can freely create the entities they need
 */
abstract class FunctionalTestCase extends WebTestCase
{
    protected static function getEntityManager(): EntityManagerInterface
    {
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);

        return $entityManager;
    }

    protected static function getPixelRepository(): PixelRepositoryInterface
    {
        $repository = static::getContainer()->get('setono_sylius_facebook.repository.pixel');
        self::assertInstanceOf(PixelRepositoryInterface::class, $repository);

        return $repository;
    }

    protected static function createChannel(string $code): ChannelInterface
    {
        $locale = self::findOrCreateCodeAwareResource(LocaleInterface::class, 'sylius.repository.locale', 'sylius.factory.locale', 'en_US');
        $currency = self::findOrCreateCodeAwareResource(CurrencyInterface::class, 'sylius.repository.currency', 'sylius.factory.currency', 'USD');

        $channel = self::createResource(ChannelInterface::class, 'sylius.factory.channel');
        $channel->setCode($code);
        $channel->setName($code);
        $channel->setEnabled(true);
        $channel->setTaxCalculationStrategy('order_items_based');
        $channel->setDefaultLocale($locale);
        $channel->addLocale($locale);
        $channel->setBaseCurrency($currency);
        $channel->addCurrency($currency);

        self::persist($channel);

        return $channel;
    }

    protected static function createPixel(string $pixelId, bool $enabled, ChannelInterface ...$channels): PixelInterface
    {
        $pixel = self::createResource(PixelInterface::class, 'setono_sylius_facebook.factory.pixel');
        $pixel->setPixelId($pixelId);
        $pixel->setAccessToken('access_token');
        $pixel->setEnabled($enabled);
        foreach ($channels as $channel) {
            $pixel->addChannel($channel);
        }

        self::persist($pixel);

        return $pixel;
    }

    protected static function createAdminUser(): AdminUserInterface
    {
        $adminUser = self::createResource(AdminUserInterface::class, 'sylius.factory.admin_user');
        $adminUser->setUsername('admin');
        $adminUser->setEmail('admin@example.com');
        $adminUser->setPlainPassword('admin');
        $adminUser->setLocaleCode('en_US');
        $adminUser->setEnabled(true);
        $adminUser->addRole('ROLE_ADMINISTRATION_ACCESS');

        self::persist($adminUser);

        return $adminUser;
    }

    protected static function persist(object ...$entities): void
    {
        $entityManager = self::getEntityManager();
        foreach ($entities as $entity) {
            $entityManager->persist($entity);
        }
        $entityManager->flush();
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    protected static function createResource(string $class, string $factoryServiceId): object
    {
        $factory = static::getContainer()->get($factoryServiceId);
        self::assertInstanceOf(FactoryInterface::class, $factory);

        $resource = $factory->createNew();
        self::assertInstanceOf($class, $resource);

        return $resource;
    }

    /**
     * @template T of CodeAwareInterface
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    private static function findOrCreateCodeAwareResource(string $class, string $repositoryServiceId, string $factoryServiceId, string $code): CodeAwareInterface
    {
        $repository = static::getContainer()->get($repositoryServiceId);
        self::assertInstanceOf(RepositoryInterface::class, $repository);

        $resource = $repository->findOneBy(['code' => $code]);
        if (null === $resource) {
            $resource = self::createResource($class, $factoryServiceId);
            $resource->setCode($code);

            self::persist($resource);
        }

        self::assertInstanceOf($class, $resource);

        return $resource;
    }
}
