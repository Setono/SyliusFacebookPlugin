<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookPlugin\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\SyliusFacebookPlugin\DependencyInjection\SetonoSyliusFacebookExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

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
    public function after_loading_the_correct_parameter_has_been_set(): void
    {
        $this->load([
            'access_token' => 'ACCESS_TOKEN',
        ]);

        $this->assertContainerBuilderHasParameter('setono_sylius_facebook.api_version', 'v12.0');
        $this->assertContainerBuilderHasParameter('setono_sylius_facebook.access_token', 'ACCESS_TOKEN');
        $this->assertContainerBuilderHasParameter('setono_sylius_facebook.send_delay', 300);
        $this->assertContainerBuilderHasParameter('setono_sylius_facebook.driver', SyliusResourceBundle::DRIVER_DOCTRINE_ORM);
    }
}