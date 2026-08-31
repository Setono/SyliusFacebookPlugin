<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Functional\Admin;

use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Tests\Functional\FunctionalTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Security\Core\User\UserInterface;

final class ManagingPixelsTest extends FunctionalTestCase
{
    /**
     * @test
     */
    public function it_lists_pixels(): void
    {
        $client = $this->createAuthenticatedClient();
        self::createPixel('123456789', true, self::createChannel('WEB'));

        $client->request('GET', '/admin/facebook/pixels/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('table', '123456789');
    }

    /**
     * @test
     */
    public function it_creates_a_pixel(): void
    {
        $client = $this->createAuthenticatedClient();
        self::createChannel('WEB');

        $client->request('GET', '/admin/facebook/pixels/new');
        self::assertResponseIsSuccessful();

        $client->submitForm('Create', [
            'setono_sylius_facebook_pixel[pixelId]' => '123456789',
            'setono_sylius_facebook_pixel[accessToken]' => 'access_token',
            'setono_sylius_facebook_pixel[enabled]' => '1',
            'setono_sylius_facebook_pixel[channels]' => ['WEB'],
        ]);

        self::assertResponseRedirects();
        $client->followRedirect();
        self::assertResponseIsSuccessful();

        $pixel = self::getPixelRepository()->findOneBy(['pixelId' => '123456789']);
        self::assertInstanceOf(PixelInterface::class, $pixel);
        self::assertSame('access_token', $pixel->getAccessToken());
        self::assertTrue($pixel->isEnabled());
        self::assertCount(1, $pixel->getChannels());
    }

    /**
     * @test
     */
    public function it_does_not_create_a_pixel_with_an_invalid_pixel_id(): void
    {
        $client = $this->createAuthenticatedClient();
        self::createChannel('WEB');

        $client->request('GET', '/admin/facebook/pixels/new');
        $client->submitForm('Create', [
            'setono_sylius_facebook_pixel[pixelId]' => 'not a number',
            'setono_sylius_facebook_pixel[channels]' => ['WEB'],
        ]);

        self::assertSelectorExists('.sylius-validation-error');
        self::assertNull(self::getPixelRepository()->findOneBy(['pixelId' => 'not a number']));
    }

    /**
     * @test
     */
    public function it_updates_a_pixel(): void
    {
        $client = $this->createAuthenticatedClient();
        $pixelId = (int) self::createPixel('123456789', true, self::createChannel('WEB'))->getId();

        $client->request('GET', sprintf('/admin/facebook/pixels/%d/edit', $pixelId));
        self::assertResponseIsSuccessful();

        $client->submitForm('Save changes', [
            'setono_sylius_facebook_pixel[pixelId]' => '987654321',
            'setono_sylius_facebook_pixel[enabled]' => false,
        ]);

        self::assertResponseRedirects();

        // The kernel is rebooted between requests, so fetch the pixel again instead of refreshing a detached entity
        self::getEntityManager()->clear();
        $pixel = self::getPixelRepository()->find($pixelId);
        self::assertInstanceOf(PixelInterface::class, $pixel);
        self::assertSame('987654321', $pixel->getPixelId());
        self::assertFalse($pixel->isEnabled());
    }

    /**
     * @test
     */
    public function it_deletes_a_pixel(): void
    {
        $client = $this->createAuthenticatedClient();
        $pixel = self::createPixel('123456789', true, self::createChannel('WEB'));
        $pixelId = (int) $pixel->getId();

        $client->request('GET', '/admin/facebook/pixels/');
        $client->submitForm('Delete');

        self::assertResponseRedirects();
        self::getEntityManager()->clear();
        self::assertNull(self::getPixelRepository()->find($pixelId));
    }

    /**
     * @test
     */
    public function it_requires_an_authenticated_administrator(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/facebook/pixels/');

        self::assertResponseRedirects('/admin/login');
    }

    private function createAuthenticatedClient(): KernelBrowser
    {
        $client = self::createClient();

        $adminUser = self::createAdminUser();
        self::assertInstanceOf(UserInterface::class, $adminUser);
        $client->loginUser($adminUser, 'admin');

        return $client;
    }
}
