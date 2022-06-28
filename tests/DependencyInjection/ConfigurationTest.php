<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookPlugin\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Setono\SyliusFacebookPlugin\DependencyInjection\Configuration;
use Setono\SyliusFacebookPlugin\Doctrine\ORM\PixelRepository;
use Setono\SyliusFacebookPlugin\Form\Type\PixelType;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    /**
     * @test
     */
    public function processed_value_contains_required_and_default_values(): void
    {
        $this->assertProcessedConfigurationEquals([], [
            'driver' => SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            'resources' => [
                'pixel' => [
                    'classes' => [
                        'model' => Pixel::class,
                        'controller' => ResourceController::class,
                        'repository' => PixelRepository::class,
                        'factory' => Factory::class,
                        'form' => PixelType::class,
                    ],
                ],
            ],
        ]);
    }
}
