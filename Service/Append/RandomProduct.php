<?php

namespace Perspective\Lighthouse\Service\Append;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Perspective\Lighthouse\Api\Data\PageTypeToAppendInterface;

class RandomProduct implements PageTypeToAppendInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private CollectionFactory $productCollectionFactory;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(CollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function append($urls): array
    {
        $collection = $this->productCollectionFactory->create();
        if (!$collection->getSize() > 0) {
            return $urls;
        }
        //clean up the collection to avoid unforeseen issues
        $collection->addFieldToFilter('status', 1);
        $collection->addFieldToFilter('visibility', ['in' => [2, 3, 4]]);
        $collection->addAttributeToSelect('*')
            ->setPageSize(3);
        $collection->getSelect()->orderRand();
        $product = $collection->getFirstItem();
        $urls[$this->getPageTypeName()] = $product->getProductUrl();
        return $urls;
    }

    public function getPageTypeName(): string
    {
        return 'product';
    }
}
