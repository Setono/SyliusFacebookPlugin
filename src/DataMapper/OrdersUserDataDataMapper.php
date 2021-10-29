<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use FacebookAds\Object\ServerSide\Gender;
use function Safe\preg_replace;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

/* not final */ class OrdersUserDataDataMapper implements DataMapperInterface
{
    /**
     * @psalm-assert-if-true OrderInterface $source
     */
    public function supports($source, ServerSideEventInterface $target, array $context = []): bool
    {
        return $source instanceof OrderInterface;
    }

    /**
     * @param OrderInterface $source
     */
    public function map($source, ServerSideEventInterface $target, array $context = []): void
    {
        /** @var Customer|null $customer */
        $customer = $source->getCustomer();
        if (null === $customer) {
            return;
        }

        $userData = $target->getUserData();
        $userData
            ->setEmails($this->getEmails($customer))
            ->setPhones($this->getPhones($customer))
        ;

        $userData
            ->setFirstName($customer->getFirstName())
            ->setLastName($customer->getLastName())
        ;

        $gender = $customer->getGender();
        if (in_array($gender, [Gender::FEMALE, Gender::MALE])) {
            $userData->setGender($gender);
        }
    }

    protected function getEmails(CustomerInterface $customer): array
    {
        $emails = [
            $customer->getEmailCanonical(),
        ];

        $emails = array_merge($emails, $this->getAdditionalEmails());

        return array_unique(
            array_filter($emails)
        );
    }

    /** To be overriden */
    protected function getAdditionalEmails(): array
    {
        return [];
    }

    protected function getPhones(CustomerInterface $customer): array
    {
        $phones = [
            $customer->getPhoneNumber(),
        ];

        /** @var AddressInterface $address */
        foreach ($customer->getAddresses() as $address) {
            $phones[] = $address->getPhoneNumber();
        }

        $phones = array_merge($phones, $this->getAdditionalPhones());

        return array_unique(
            array_map(
                static fn (string $phone): string => preg_replace('/^[0-9]/', '', $phone),
                array_filter($phones)
            )
        );
    }

    /** To be overriden */
    protected function getAdditionalPhones(): array
    {
        return [];
    }
}
