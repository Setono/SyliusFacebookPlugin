<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookTrackingPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Setono\SyliusFacebookTrackingPlugin\Context\FacebookConfigContextInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Tests\Setono\SyliusFacebookTrackingPlugin\Behat\Page\Admin\FacebookConfig\UpdatePageInterface;

final class FacebookConfigContext implements Context
{
    /** @var UpdatePageInterface */
    private $updatePage;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var FacebookConfigContextInterface */
    private $facebookConfigContext;

    public function __construct(
        UpdatePageInterface $updatePage,
        NotificationCheckerInterface $notificationChecker,
        FacebookConfigContextInterface $facebookConfigContext
    ) {
        $this->updatePage = $updatePage;
        $this->notificationChecker = $notificationChecker;
        $this->facebookConfigContext = $facebookConfigContext;
    }

    /**
     * @When I go to the create config page
     */
    public function iGoToTheCreateConfigPage(): void
    {
        $this->updatePage->open(['id' => $this->facebookConfigContext->getConfig()->getId()]);
    }

    /**
     * @When I fill the code with :code
     */
    public function iFillTheCodeWith(string $code): void
    {
        $this->updatePage->fillCode($code);
    }

    /**
     * @When I name it :name
     */
    public function iNameIt(string $name): void
    {
        $this->updatePage->fillName($name);
    }

    /**
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        $this->notificationChecker->checkNotification('has been successfully updated.', NotificationType::success());
    }
}
