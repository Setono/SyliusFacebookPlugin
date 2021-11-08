<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class PixelFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'setono_facebook_pixel';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $node = $resourceNode->children();
        $node->scalarNode('pixel_id')->cannotBeEmpty();
        $node->booleanNode('enabled')->defaultTrue();
        $node->arrayNode('channels')->scalarPrototype();
    }
}
