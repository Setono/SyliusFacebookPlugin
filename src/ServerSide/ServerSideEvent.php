<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\ServerSide;

use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\UserData;

/**
 * FacebookAds library have issues at types declarations
 * so we have to suppress a lot
 */
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

    public function getEventName(): string
    {
        return parent::getEventName();
    }

    /**
     * @psalm-suppress InvalidReturnType
     */
    public function getUserData(): UserData
    {
        /**
         * @var UserData|null
         * @psalm-suppress UndefinedDocblockClass
         */
        $userData = parent::getUserData();
        if (null === $userData) {
            $userData = new UserData();
            /** @psalm-suppress InvalidArgument */
            parent::setUserData($userData);
        }

        /** @psalm-suppress InvalidReturnStatement */
        return $userData;
    }

    /**
     * @psalm-suppress InvalidReturnType
     */
    public function getCustomData(): CustomData
    {
        /**
         * @var CustomData|null
         * @psalm-suppress UndefinedDocblockClass
         */
        $customData = parent::getCustomData();
        if (null === $customData) {
            $customData = new CustomData();
            /** @psalm-suppress InvalidArgument */
            parent::setCustomData($customData);
        }

        /** @psalm-suppress InvalidReturnStatement */
        return $customData;
    }
}
