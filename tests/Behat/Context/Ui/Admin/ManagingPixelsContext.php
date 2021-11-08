<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Tests\Setono\SyliusFacebookPlugin\Behat\Page\Admin\Pixel\CreatePixelPage;
use Tests\Setono\SyliusFacebookPlugin\Behat\Page\Admin\Pixel\IndexPixelPage;
use Tests\Setono\SyliusFacebookPlugin\Behat\Page\Admin\Pixel\UpdatePixelPage;
use Webmozart\Assert\Assert;

final class ManagingPixelsContext implements Context
{
    private IndexPixelPage $indexPixelPage;

    private CreatePixelPage $createPixelPage;

    private UpdatePixelPage $updatePixelPage;

    public function __construct(IndexPixelPage $indexPixelPage, CreatePixelPage $createPixelPage, UpdatePixelPage $updatePixelPage)
    {
        $this->indexPixelPage = $indexPixelPage;
        $this->createPixelPage = $createPixelPage;
        $this->updatePixelPage = $updatePixelPage;
    }

    /**
     * @Given I want to create a new pixel
     */
    public function iWantToCreateANewPixel(): void
    {
        $this->createPixelPage->open();
    }

    /**
     * @When I fill the pixel id with :id
     */
    public function iFillThePixelId(int $id): void
    {
        $this->createPixelPage->specifyPixelId($id);
    }

    /**
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->createPixelPage->create();
    }

    /**
     * @Then the pixel :pixelId should appear in the store
     */
    public function thePropertyShouldAppearInTheStore(string $pixelId): void
    {
        $this->indexPixelPage->open();

        Assert::true(
            $this->indexPixelPage->isSingleResourceOnPage(['pixelId' => $pixelId]),
            sprintf('Pixel %s should exist but it does not', $pixelId)
        );
    }

    /**
     * @Given I want to update the pixel with pixel id :pixel
     */
    public function iWantToUpdateThePropertyWithTrackingId(PixelInterface $pixel): void
    {
        $this->updatePixelPage->open([
            'id' => $pixel->getId(),
        ]);
    }

    /**
     * @When I update the pixel with pixel id :pixelId
     */
    public function iUpdateThePropertyWithTrackingId(string $pixelId): void
    {
        $this->updatePixelPage->specifyPixelId($pixelId);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePixelPage->saveChanges();
    }

    /**
     * @Then this pixel's pixel id should be :pixelId
     */
    public function thisPropertysTrackingIdShouldBe(string $pixelId): void
    {
        Assert::eq($pixelId, $this->updatePixelPage->getPixelId());
    }
}
