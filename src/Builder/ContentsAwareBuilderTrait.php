<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Builder;

use function assert;
use InvalidArgumentException;
use function Safe\sprintf;

/**
 * @mixin Builder
 */
trait ContentsAwareBuilderTrait
{
    /**
     * @param array|BuilderInterface $content
     */
    public function addContent($content): self
    {
        assert($this instanceof Builder);

        if ($content instanceof BuilderInterface) {
            $content = $content->getData();
        }

        if (!is_array($content)) {
            throw new InvalidArgumentException(sprintf(
                'The $content parameter needs to be an array or instance of %s', BuilderInterface::class
            ));
        }

        $this->data['contents'][] = $content;

        return $this;
    }
}
