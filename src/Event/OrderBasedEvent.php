<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Event;

use Setono\MetaConversionsApi\Event\Content;
use Setono\MetaConversionsApi\Event\Event;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

abstract class OrderBasedEvent extends Event
{
    use FormatAmountTrait;

    public function __construct(OrderInterface $order, string $eventName)
    {
        parent::__construct($eventName);

        $this->customData->currency = $order->getCurrencyCode();
        $this->customData->value = self::formatAmount($order->getTotal());
        $this->customData->contentType = 'product';
        $this->customData->contentIds = $this->getContentIds($order);
        $this->customData->contents = $this->getContents($order);
        $this->customData->numItems = array_sum($order->getItems()->map(function (OrderItemInterface $orderItem): int {
            return $orderItem->getQuantity();
        })->toArray());

        $this->populateCustomerInformation($order);
        $this->populateBillingInformation($order);
    }

    /**
     * Returns the respective product variant codes for the order items on the order
     *
     * @return list<string>
     */
    private function getContentIds(OrderInterface $order): array
    {
        $codes = [];

        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            if (null === $variant) {
                continue;
            }

            $variantCode = $variant->getCode();
            if (null !== $variantCode) {
                $codes[] = $variantCode;
            }
        }

        return $codes;
    }

    /**
     * @return list<Content>
     */
    private function getContents(OrderInterface $order): array
    {
        $contents = [];
        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            if (null === $variant) {
                continue;
            }

            $contents[] = new Content(
                (string) $variant->getCode(),
                $item->getQuantity(),
                self::formatAmount($item->getDiscountedUnitPrice())
            );
        }

        return $contents;
    }

    private function populateCustomerInformation(OrderInterface $order): void
    {
        $customer = $order->getCustomer();
        if (null === $customer) {
            return;
        }

        $this->userData->email[] = (string) $customer->getEmailCanonical();
        $this->userData->phoneNumber[] = (string) $customer->getPhoneNumber();

        $gender = $customer->getGender();
        if (in_array($gender, ['f', 'm'], true)) {
            $this->userData->gender[] = $gender;
        }
    }

    private function populateBillingInformation(OrderInterface $order): void
    {
        $billingAddress = $order->getBillingAddress();
        if (null === $billingAddress) {
            return;
        }

        $this->userData->firstName[] = (string) $billingAddress->getFirstName();
        $this->userData->lastName[] = (string) $billingAddress->getLastName();
        $this->userData->phoneNumber[] = (string) $billingAddress->getPhoneNumber();
        $this->userData->zipCode[] = (string) $billingAddress->getPostcode();
        $this->userData->city[] = (string) $billingAddress->getCity();
        $this->userData->country[] = (string) $billingAddress->getCountryCode();
    }
}
