<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Event;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

abstract class OrderBasedEventTestCase extends TestCase
{
    use ProphecyTrait;

    /**
     * @param list<OrderItemInterface> $items
     */
    protected function createOrder(
        array $items = [],
        int $total = 0,
        ?CustomerInterface $customer = null,
        ?AddressInterface $billingAddress = null,
    ): OrderInterface {
        $order = $this->prophesize(OrderInterface::class);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getTotal()->willReturn($total);
        $order->getItems()->willReturn(new ArrayCollection($items));
        $order->getCustomer()->willReturn($customer);
        $order->getBillingAddress()->willReturn($billingAddress);

        return $order->reveal();
    }

    protected function createOrderItem(string $variantCode, int $quantity, int $discountedUnitPrice, int $total): OrderItemInterface
    {
        $orderItem = $this->prophesize(OrderItemInterface::class);
        $orderItem->getVariant()->willReturn($this->createProductVariant($variantCode));
        $orderItem->getQuantity()->willReturn($quantity);
        $orderItem->getDiscountedUnitPrice()->willReturn($discountedUnitPrice);
        $orderItem->getTotal()->willReturn($total);

        return $orderItem->reveal();
    }

    protected function createProductVariant(?string $code): ProductVariantInterface
    {
        $variant = $this->prophesize(ProductVariantInterface::class);
        $variant->getCode()->willReturn($code);

        return $variant->reveal();
    }

    protected function createCustomer(?string $gender): CustomerInterface
    {
        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmailCanonical()->willReturn('john.doe@example.com');
        $customer->getPhoneNumber()->willReturn('+4512345678');
        $customer->getGender()->willReturn($gender);

        return $customer->reveal();
    }

    protected function createBillingAddress(): AddressInterface
    {
        $address = $this->prophesize(AddressInterface::class);
        $address->getFirstName()->willReturn('John');
        $address->getLastName()->willReturn('Doe');
        $address->getPhoneNumber()->willReturn('+4587654321');
        $address->getPostcode()->willReturn('8000');
        $address->getCity()->willReturn('Aarhus');
        $address->getCountryCode()->willReturn('DK');

        return $address->reveal();
    }
}
