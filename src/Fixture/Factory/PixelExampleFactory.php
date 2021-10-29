<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Fixture\Factory;

use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PixelExampleFactory extends AbstractExampleFactory
{
    private FactoryInterface $pixelFactory;

    private ChannelRepositoryInterface $channelRepository;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        FactoryInterface $pixelFactory,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->pixelFactory = $pixelFactory;
        $this->channelRepository = $channelRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): PixelInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var PixelInterface $pixel */
        $pixel = $this->pixelFactory->createNew();
        $pixel->setPixelId((string) $options['pixel_id']);
        $pixel->setEnabled($options['enabled']);

        foreach ($options['channels'] as $channel) {
            $pixel->addChannel($channel);
        }

        return $pixel;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined('pixel_id')
            ->setAllowedTypes('pixel_id', 'numeric')

            ->setDefined('enabled')
            ->setAllowedTypes('enabled', 'bool')

            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
        ;
    }
}
