<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookTrackingPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;

final class FacebookConfigContext implements Context
{


    /**
     * @When I go to the create config page
     */
    public function iGoToTheCreateConfigPage()
    {
        throw new PendingException();
    }

    /**
     * @When I fill the code with :arg1
     */
    public function iFillTheCodeWith($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I name it :arg1
     */
    public function iNameIt($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        throw new PendingException();
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated()
    {
        throw new PendingException();
    }
}
