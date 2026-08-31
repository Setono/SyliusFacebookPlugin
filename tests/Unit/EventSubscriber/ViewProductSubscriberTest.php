<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\EventSubscriber;

use Setono\SyliusFacebookPlugin\Event\ProductViewedEvent;
use Setono\SyliusFacebookPlugin\EventSubscriber\ViewProductSubscriber;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

final class ViewProductSubscriberTest extends EventSubscriberTestCase
{
    /**
     * @test
     */
    public function it_subscribes_to_the_product_show_event(): void
    {
        self::assertSame(['sylius.product.show' => 'track'], ViewProductSubscriber::getSubscribedEvents());
    }

    /**
     * @test
     */
    public function it_raises_a_product_viewed_event(): void
    {
        $this->expectConversionsApiEvent(ProductViewedEvent::class);

        $this->createSubscriber()->track(new ResourceControllerEvent($this->createProduct()));
    }

    /**
     * @test
     */
    public function it_logs_an_error_instead_of_failing_when_the_subject_is_not_a_product(): void
    {
        $this->expectLoggedError('Expected an instance of Sylius\Component\Core\Model\ProductInterface');

        $this->createSubscriber()->track(new ResourceControllerEvent(new \stdClass()));
    }

    private function createSubscriber(): ViewProductSubscriber
    {
        $subscriber = new ViewProductSubscriber($this->eventDispatcher->reveal());
        $subscriber->setLogger($this->logger->reveal());

        return $subscriber;
    }
}
