<?php

namespace Perspective\Lighthouse\Service\Append;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Perspective\Lighthouse\Api\Data\PageTypeToAppendInterface;

class RandomCategory implements PageTypeToAppendInterface
{
    private CollectionFactory $categoryFactory;

    private \Magento\Store\Api\Data\StoreInterface $store;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory
     */
    public function __construct(CollectionFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    public function append($urls): array
    {
        $collection = $this->categoryFactory->create();
        if (!$collection->getSize() > 0) {
            return $urls;
        }
        //clean up the collection to avoid unforeseen issues
        $collection->addFieldToFilter('is_active', 1);
        $collection->addAttributeToSelect('*')
            ->setPageSize(3);
        $collection->getSelect()->orderRand();
        $category = $collection->getFirstItem();
        if ((int)$category->getId() !== 2) {
            $urls[$this->getPageTypeName() . '@' . $this->getStore()->getCode()] = $category->getUrl();
        }
        return $urls;
    }

    public function getPageTypeName(): string
    {
        return 'category';
    }


    public function setStore($store): void
    {
        $this->store = $store;
    }

    public function getStore()
    {
        return $this->store;
    }
}
