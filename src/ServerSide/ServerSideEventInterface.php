<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\ServerSide;

use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\UserData;

/**
 * @mixin Event
 *
 * @see https://developers.facebook.com/docs/facebook-pixel/reference/
 */
interface ServerSideEventInterface
{
    public const CONTENT_TYPE_PRODUCT = 'product';

    public const CONTENT_TYPE_PRODUCT_GROUP = 'product_group';

    public const EVENT_ADD_TO_CART = 'AddToCart';

    public const EVENT_ADD_PAYMENT_INFO = 'AddPaymentInfo';

    public const EVENT_ADD_TO_WISHLIST = 'AddToWishlist';

    public const EVENT_COMPLETE_REGISTRATION = 'CompleteRegistration';

    public const EVENT_CONTACT = 'Contact';

    public const EVENT_CUSTOMIZE_PRODUCT = 'CustomizeProduct';

    public const EVENT_DONATE = 'Donate';

    public const EVENT_FIND_LOCATION = 'FindLocation';

    public const EVENT_INITIATE_CHECKOUT = 'InitiateCheckout';

    public const EVENT_LEAD = 'Lead';

    public const EVENT_PURCHASE = 'Purchase';

    public const EVENT_SCHEDULE = 'Schedule';

    public const EVENT_SEARCH = 'Search';

    public const EVENT_START_TRIAL = 'StartTrial';

    public const EVENT_SUBMIT_APPLICATION = 'SubmitApplication';

    public const EVENT_SUBSCRIBE = 'Subscribe';

    public const EVENT_VIEW_CONTENT = 'ViewContent';

    public const CUSTOM_EVENT_VIEW_CATEGORY = 'ViewCategory';

    public function getEventName(): string;

    public function getUserData(): UserData;

    public function getCustomData(): CustomData;

    /**
     * @return array
     */
    public function normalize();
}
