<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookPlugin\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Setono\SyliusFacebookPlugin\DependencyInjection\Configuration;
use Setono\SyliusFacebookPlugin\Doctrine\ORM\PixelEventRepository;
use Setono\SyliusFacebookPlugin\Doctrine\ORM\PixelRepository;
use Setono\SyliusFacebookPlugin\Form\Type\PixelType;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Setono\SyliusFacebookPlugin\Model\PixelEvent;
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
        $this->assertProcessedConfigurationEquals([[
            'access_token' => 'ACCESS_TOKEN',
        ]], [
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
                'pixel_event' => [
                    'classes' => [
                        'model' => PixelEvent::class,
                        'controller' => ResourceController::class,
                        'repository' => PixelEventRepository::class,
                        'factory' => Factory::class,
                    ],
                ],
            ],
            'access_token' => 'ACCESS_TOKEN',
            'test_event_code' => null,
            'api_version' => 'v13.0',
            'send_delay' => 300,
            'cleanup_delay' => 2592000,
            'fbc_ttl' => 2419200,
            'fbp_ttl' => 31536000,
        ]);
    }
}
