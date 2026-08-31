<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Form\Type;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusFacebookPlugin\Form\Type\PixelType;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

final class PixelTypeTest extends TypeTestCase
{
    use ProphecyTrait;

    private Channel $channel;

    protected function setUp(): void
    {
        $this->channel = new Channel();
        $this->channel->setCode('WEB');
        $this->channel->setName('Web');

        parent::setUp();
    }

    /**
     * @return list<FormExtensionInterface>
     */
    protected function getExtensions(): array
    {
        /** @var ObjectProphecy<RepositoryInterface<ChannelInterface>> $channelRepository */
        $channelRepository = $this->prophesize(RepositoryInterface::class);
        $channelRepository->findAll()->willReturn([$this->channel]);

        return [
            new PreloadedExtension([
                new PixelType(Pixel::class, ['setono_sylius_facebook']),
                new ChannelChoiceType($channelRepository->reveal()),
            ], []),
        ];
    }

    /**
     * @test
     */
    public function it_builds_the_pixel_form(): void
    {
        $view = $this->factory->create(PixelType::class)->createView();

        self::assertSame('setono_sylius_facebook_pixel', self::vars($view)['name']);

        $pixelId = self::vars($view->children['pixelId']);
        self::assertSame('setono_sylius_facebook.form.pixel.pixel_id', $pixelId['label']);
        self::assertSame('setono_sylius_facebook.form.pixel.pixel_id_help', $pixelId['help']);
        self::assertSame(['placeholder' => 'setono_sylius_facebook.form.pixel.pixel_id_placeholder'], $pixelId['attr']);
        self::assertTrue($pixelId['required']);

        $accessToken = self::vars($view->children['accessToken']);
        self::assertSame('setono_sylius_facebook.form.pixel.access_token', $accessToken['label']);
        self::assertSame(['rows' => 3, 'placeholder' => 'setono_sylius_facebook.form.pixel.access_token_placeholder'], $accessToken['attr']);
        self::assertFalse($accessToken['required']);

        $enabled = self::vars($view->children['enabled']);
        self::assertSame('sylius.ui.enabled', $enabled['label']);
        self::assertFalse($enabled['required']);

        $channels = self::vars($view->children['channels']);
        self::assertSame('sylius.ui.channels', $channels['label']);
        self::assertTrue($channels['multiple']);
        self::assertTrue($channels['expanded']);
        self::assertCount(1, $view->children['channels']->children);
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function vars(FormView $view): array
    {
        $vars = $view->vars;
        self::assertIsArray($vars);

        return $vars;
    }

    /**
     * @test
     */
    public function it_maps_submitted_data_to_a_pixel(): void
    {
        $form = $this->factory->create(PixelType::class);
        $form->submit([
            'pixelId' => '123456789',
            'accessToken' => 'access_token',
            'enabled' => '1',
            'channels' => ['WEB'],
        ]);

        self::assertTrue($form->isSynchronized());

        $pixel = $form->getData();
        self::assertInstanceOf(Pixel::class, $pixel);
        self::assertSame('123456789', $pixel->getPixelId());
        self::assertSame('access_token', $pixel->getAccessToken());
        self::assertTrue($pixel->isEnabled());
        self::assertTrue($pixel->hasChannel($this->channel));
    }
}
