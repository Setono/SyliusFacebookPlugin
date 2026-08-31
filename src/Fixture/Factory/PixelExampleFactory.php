<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

class PixelExampleFactory extends AbstractExampleFactory
{
    private readonly Generator $faker;

    private readonly OptionsResolver $optionsResolver;

    /**
     * @param FactoryInterface<PixelInterface> $pixelFactory
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private readonly FactoryInterface $pixelFactory,
        private readonly ChannelRepositoryInterface $channelRepository,
    ) {
        $this->faker = Factory::create();

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
    }

    /**
     * @param array<array-key, mixed> $options
     */
    public function create(array $options = []): PixelInterface
    {
        $options = $this->optionsResolver->resolve($options);

        $pixel = $this->pixelFactory->createNew();
        if (array_key_exists('pixel_id', $options)) {
            Assert::numeric($options['pixel_id']);

            $pixel->setPixelId((string) $options['pixel_id']);
        }

        if (array_key_exists('access_token', $options)) {
            Assert::string($options['access_token']);

            $pixel->setAccessToken($options['access_token']);
        } else {
            $pixel->setAccessToken($this->faker->randomAscii);
        }

        if (array_key_exists('enabled', $options)) {
            Assert::boolean($options['enabled']);

            $pixel->setEnabled($options['enabled']);
        }

        if (array_key_exists('channels', $options)) {
            Assert::isIterable($options['channels']);

            foreach ($options['channels'] as $channel) {
                Assert::isInstanceOf($channel, ChannelInterface::class);

                $pixel->addChannel($channel);
            }
        }

        return $pixel;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined('pixel_id')
            ->setAllowedTypes('pixel_id', 'numeric')

            ->setDefined('access_token')
            ->setAllowedTypes('access_token', 'string')

            ->setDefined('enabled')
            ->setAllowedTypes('enabled', 'bool')

            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
        ;
    }
}
