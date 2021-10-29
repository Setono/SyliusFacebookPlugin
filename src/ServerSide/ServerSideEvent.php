<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\ServerSide;

use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\UserData;

class ServerSideEvent extends Event implements ServerSideEventInterface
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->setActionSource(ActionSource::WEBSITE)
            ->setEventTime(time())
        ;
    }

    public function getUserData(): UserData
    {
        $userData = parent::getUserData();
        if (null === $userData) {
            $userData = new UserData();
            parent::setUserData($userData);
        }

        return $userData;
    }

    public function getCustomData(): CustomData
    {
        $customData = parent::getCustomData();
        if (null === $customData) {
            $customData = new CustomData();
            parent::setCustomData($customData);
        }

        return $customData;
    }
}
