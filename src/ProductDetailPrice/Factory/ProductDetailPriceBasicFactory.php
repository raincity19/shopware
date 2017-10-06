<?php declare(strict_types=1);

namespace Shopware\ProductDetailPrice\Factory;

use Doctrine\DBAL\Connection;
use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Factory\ExtensionRegistryInterface;
use Shopware\Framework\Factory\Factory;
use Shopware\ProductDetailPrice\Extension\ProductDetailPriceExtension;
use Shopware\ProductDetailPrice\Struct\ProductDetailPriceBasicStruct;
use Shopware\Search\QueryBuilder;
use Shopware\Search\QuerySelection;

class ProductDetailPriceBasicFactory extends Factory
{
    const ROOT_NAME = 'product_detail_price';
    const EXTENSION_NAMESPACE = 'productDetailPrice';

    const FIELDS = [
       'uuid' => 'uuid',
       'customerGroupUuid' => 'customer_group_uuid',
       'quantityStart' => 'quantity_start',
       'quantityEnd' => 'quantity_end',
       'productDetailUuid' => 'product_detail_uuid',
       'price' => 'price',
       'pseudoPrice' => 'pseudo_price',
       'basePrice' => 'base_price',
       'percentage' => 'percentage',
       'createdAt' => 'created_at',
       'updatedAt' => 'updated_at',
    ];

    public function __construct(
        Connection $connection,
        ExtensionRegistryInterface $registry
    ) {
        parent::__construct($connection, $registry);
    }

    public function hydrate(
        array $data,
        ProductDetailPriceBasicStruct $productDetailPrice,
        QuerySelection $selection,
        TranslationContext $context
    ): ProductDetailPriceBasicStruct {
        $productDetailPrice->setUuid((string) $data[$selection->getField('uuid')]);
        $productDetailPrice->setCustomerGroupUuid((string) $data[$selection->getField('customerGroupUuid')]);
        $productDetailPrice->setQuantityStart((int) $data[$selection->getField('quantityStart')]);
        $productDetailPrice->setQuantityEnd(isset($data[$selection->getField('quantity_end')]) ? (int) $data[$selection->getField('quantityEnd')] : null);
        $productDetailPrice->setProductDetailUuid((string) $data[$selection->getField('productDetailUuid')]);
        $productDetailPrice->setPrice((float) $data[$selection->getField('price')]);
        $productDetailPrice->setPseudoPrice(isset($data[$selection->getField('pseudo_price')]) ? (float) $data[$selection->getField('pseudoPrice')] : null);
        $productDetailPrice->setBasePrice(isset($data[$selection->getField('base_price')]) ? (float) $data[$selection->getField('basePrice')] : null);
        $productDetailPrice->setPercentage(isset($data[$selection->getField('percentage')]) ? (float) $data[$selection->getField('percentage')] : null);
        $productDetailPrice->setCreatedAt(isset($data[$selection->getField('created_at')]) ? new \DateTime($data[$selection->getField('createdAt')]) : null);
        $productDetailPrice->setUpdatedAt(isset($data[$selection->getField('updated_at')]) ? new \DateTime($data[$selection->getField('updatedAt')]) : null);

        /** @var $extension ProductDetailPriceExtension */
        foreach ($this->getExtensions() as $extension) {
            $extension->hydrate($productDetailPrice, $data, $selection, $context);
        }

        return $productDetailPrice;
    }

    public function getFields(): array
    {
        $fields = array_merge(self::FIELDS, parent::getFields());

        return $fields;
    }

    public function joinDependencies(QuerySelection $selection, QueryBuilder $query, TranslationContext $context): void
    {
        $this->joinTranslation($selection, $query, $context);

        $this->joinExtensionDependencies($selection, $query, $context);
    }

    public function getAllFields(): array
    {
        $fields = array_merge(self::FIELDS, $this->getExtensionFields());

        return $fields;
    }

    protected function getRootName(): string
    {
        return self::ROOT_NAME;
    }

    protected function getExtensionNamespace(): string
    {
        return self::EXTENSION_NAMESPACE;
    }

    private function joinTranslation(
        QuerySelection $selection,
        QueryBuilder $query,
        TranslationContext $context
    ): void {
        if (!($translation = $selection->filter('translation'))) {
            return;
        }
        $query->leftJoin(
            $selection->getRootEscaped(),
            'product_detail_price_translation',
            $translation->getRootEscaped(),
            sprintf(
                '%s.product_detail_price_uuid = %s.uuid AND %s.language_uuid = :languageUuid',
                $translation->getRootEscaped(),
                $selection->getRootEscaped(),
                $translation->getRootEscaped()
            )
        );
        $query->setParameter('languageUuid', $context->getShopUuid());
    }
}