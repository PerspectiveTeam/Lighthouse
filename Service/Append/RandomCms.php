<?php

namespace Perspective\Lighthouse\Service\Append;

use Magento\Cms\Helper\Page;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Perspective\Lighthouse\Api\Data\PageTypeToAppendInterface;

class RandomCms implements PageTypeToAppendInterface
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    private CollectionFactory $cmsCollectionFactory;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    private Page $cmsHelper;

    private \Magento\Store\Api\Data\StoreInterface $store;

    public function __construct(
        CollectionFactory $cmsCollectionFactory,
        Page $cmsHelper
    ) {
        $this->cmsCollectionFactory = $cmsCollectionFactory;
        $this->cmsHelper = $cmsHelper;
    }

    public function append($urls): array
    {
        $collection = $this->cmsCollectionFactory->create();
        if (!$collection->getSize() > 0) {
            return $urls;
        }
        //clean up the collection to avoid unforeseen issues
        $collection->addFieldToFilter('is_active', 1);
        $collection->setPageSize(3);
        $collection->getSelect()->orderRand();
        /** @var \Magento\Cms\Model\Page $cmsPage */
        $cmsPage = $collection->getFirstItem();
        $urls[$this->getPageTypeName() . '@' . $this->getStore()->getCode()] = $this->cmsHelper->getPageUrl((string)$cmsPage->getId());
        return $urls;
    }

    public function getPageTypeName(): string
    {
        return 'cms_page';
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
