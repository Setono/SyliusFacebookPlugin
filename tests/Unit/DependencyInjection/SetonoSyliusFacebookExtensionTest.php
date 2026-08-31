<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\SyliusFacebookPlugin\Context\PixelContext;
use Setono\SyliusFacebookPlugin\DependencyInjection\SetonoSyliusFacebookExtension;
use Setono\SyliusFacebookPlugin\Menu\AdminMenuListener;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Setono\SyliusFacebookPlugin\Provider\DoctrineBasedPixelProvider;
use Setono\SyliusFacebookPlugin\Repository\PixelRepository;

final class SetonoSyliusFacebookExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new SetonoSyliusFacebookExtension(),
        ];
    }

    /**
     * @test
     */
    public function it_registers_the_pixel_resource(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('setono_sylius_facebook.driver', 'doctrine/orm');
        $this->assertContainerBuilderHasParameter('setono_sylius_facebook.model.pixel.class', Pixel::class);
        $this->assertContainerBuilderHasService('setono_sylius_facebook.repository.pixel', PixelRepository::class);
        $this->assertContainerBuilderHasService('setono_sylius_facebook.factory.pixel');
        $this->assertContainerBuilderHasAlias('setono_sylius_facebook.manager.pixel');
    }

    /**
     * @test
     */
    public function it_uses_the_configured_model_class(): void
    {
        $this->load(['resources' => ['pixel' => ['classes' => ['model' => 'App\Entity\Pixel']]]]);

        $this->assertContainerBuilderHasParameter('setono_sylius_facebook.model.pixel.class', 'App\Entity\Pixel');
    }

    /**
     * @test
     */
    public function it_registers_services(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('setono_sylius_facebook.context.pixel', PixelContext::class);
        $this->assertContainerBuilderHasService('setono_sylius_facebook.provider.doctrine_based_pixel_provider', DoctrineBasedPixelProvider::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag('setono_sylius_facebook.form.type.pixel', 'form.type');
        $this->assertContainerBuilderHasServiceDefinitionWithTag('setono_sylius_facebook.fixture.pixel', 'sylius_fixtures.fixture');
        $this->assertContainerBuilderHasServiceDefinitionWithTag(AdminMenuListener::class, 'kernel.event_listener', [
            'event' => 'sylius.menu.admin.main',
            'method' => 'addAdminMenuItems',
        ]);

        foreach (['add_to_cart', 'purchase', 'start_checkout', 'view_category', 'view_product'] as $subscriber) {
            $this->assertContainerBuilderHasServiceDefinitionWithTag(
                sprintf('setono_sylius_facebook.event_subscriber.%s', $subscriber),
                'kernel.event_subscriber',
            );
        }
    }
}
