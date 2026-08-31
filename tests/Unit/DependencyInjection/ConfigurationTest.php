<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Setono\SyliusFacebookPlugin\DependencyInjection\Configuration;
use Setono\SyliusFacebookPlugin\Form\Type\PixelType;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Setono\SyliusFacebookPlugin\Repository\PixelRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
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
    public function it_has_default_resource_classes(): void
    {
        $this->assertProcessedConfigurationEquals([[]], [
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

    /**
     * @test
     */
    public function it_allows_overriding_a_resource_class(): void
    {
        $this->assertProcessedConfigurationEquals([
            ['resources' => ['pixel' => ['classes' => ['model' => 'App\Entity\Pixel']]]],
        ], [
            'resources' => [
                'pixel' => [
                    'classes' => [
                        'model' => 'App\Entity\Pixel',
                        'controller' => ResourceController::class,
                        'repository' => PixelRepository::class,
                        'factory' => Factory::class,
                        'form' => PixelType::class,
                    ],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_does_not_allow_an_empty_model_class(): void
    {
        $this->assertConfigurationIsInvalid([
            ['resources' => ['pixel' => ['classes' => ['model' => '']]]],
        ], 'cannot contain an empty value');
    }
}
