<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use FacebookAds\Object\ServerSide\Gender;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface as CoreCustomerInterface;
use Sylius\Component\Core\Model\OrderInterface as CoreOrderInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Webmozart\Assert\Assert;

/* not final */ class OrdersUserDataDataMapper implements DataMapperInterface
{
    /**
     * @psalm-assert-if-true OrderInterface $source
     */
    public function supports(object $source, ServerSideEventInterface $target, array $context = []): bool
    {
        return $source instanceof OrderInterface;
    }

    public function map(object $source, ServerSideEventInterface $target, array $context = []): void
    {
        Assert::true($this->supports($source, $target, $context));

        if (!$source instanceof CoreOrderInterface) {
            return;
        }

        /** @var CustomerInterface|null $customer */
        $customer = $source->getCustomer();
        if (null === $customer) {
            return;
        }

        $userData = $target->getUserData();
        $userData
            ->setEmails($this->getEmails($customer))
            ->setPhones($this->getPhones($customer))
        ;

        /** @psalm-suppress PossiblyNullArgument */
        $userData
            ->setFirstName($customer->getFirstName())
            ->setLastName($customer->getLastName())
        ;

        $gender = $customer->getGender();
        if (in_array($gender, [Gender::FEMALE, Gender::MALE])) {
            /** @psalm-suppress InvalidArgument */
            $userData->setGender($gender);
        }
    }

    /**
     * @return string[]
     */
    protected function getEmails(CustomerInterface $customer): array
    {
        $emails = [
            $customer->getEmailCanonical(),
        ];

        $emails = array_merge($emails, $this->getAdditionalEmails($customer));

        return $this->clearEmails($emails);
    }

    /**
     * @return array<array-key, string|null>
     */
    protected function getAdditionalEmails(CustomerInterface $customer): array
    {
        return [];
    }

    /**
     * @param array<array-key, string|null> $emails
     *
     * @return array<array-key, string>
     */
    protected function clearEmails(array $emails): array
    {
        /** @var array<array-key, string> $emails */
        $emails = array_filter($emails);
        $emails = array_unique($emails);

        return $emails;
    }

    /**
     * @return string[]
     */
    protected function getPhones(CustomerInterface $customer): array
    {
        $phones = [
            $customer->getPhoneNumber(),
        ];

        $phones = array_merge($phones, $this->getAdditionalPhones($customer));

        return $this->clearPhones($phones);
    }

    /**
     * @return array<array-key, string|null>
     */
    protected function getAdditionalPhones(CustomerInterface $customer): array
    {
        $phones = [];

        if ($customer instanceof CoreCustomerInterface) {
            /** @var AddressInterface $address */
            foreach ($customer->getAddresses() as $address) {
                $phones[] = $address->getPhoneNumber();
            }
        }

        return $phones;
    }

    /**
     * @param array<array-key, string|null> $phones
     *
     * @return array<array-key, string>
     */
    protected function clearPhones(array $phones): array
    {
        /** @var array<array-key, string> $phones */
        $phones = array_filter($phones);
        $phones = array_map(
            function (string $phone): string {
                return preg_replace('/^[0-9]/', '', $phone);
            },
            $phones
        );

        /** @var array<array-key, string> $phones */
        $phones = array_filter($phones);
        $phones = array_unique($phones);

        return $phones;
    }
}
