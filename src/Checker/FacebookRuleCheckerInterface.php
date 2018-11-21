<?php
declare(strict_types=1);
namespace Setono\SyliusFacebookTrackingPlugin\Checker;
use Sylius\Component\Core\Model\ProductInterface;
interface FacebookRuleCheckerInterface
{
    public function isEligible(ProductInterface $product, array $configuration): bool;
}