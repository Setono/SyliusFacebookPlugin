<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

use InvalidArgumentException;

trait ContentsAwareBuilderTrait
{
    /**
     * @var array
     */
    protected $data = [];

    public function addContent($content): self
    {
        if ($content instanceof BuilderInterface) {
            $content = $content->getData();
        }

        if (!is_array($content)) {
            throw new InvalidArgumentException(sprintf('The $content parameter needs to be an array or instance of %s', BuilderInterface::class));
        }

        if (!isset($this->data['contents'])) {
            $this->data['contents'] = [];
        }

        $this->data['contents'][] = $content;

        return $this;
    }
}
