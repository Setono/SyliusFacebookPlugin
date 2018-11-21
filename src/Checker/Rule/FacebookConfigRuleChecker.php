<?php
declare(strict_types=1);
namespace Setono\SyliusFacebookTrackingPlugin\Checker\Rule;
use Setono\SyliusFacebookTrackingPlugin\Checker\FacebookRuleCheckerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
final class FacebookConfigRuleChecker implements FacebookRuleCheckerInterface
{
    /** @var ProductRepositoryInterface */
    private $productRepository;
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    public function isEligible(ProductInterface $product, array $configuration): bool
    {
        if (!$product instanceof ProductInterface) {
            throw new UnsupportedTypeException($product, ProductInterface::class);
        }
    }
}