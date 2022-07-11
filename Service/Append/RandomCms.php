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
        $collection->addFieldToFilter('is_active', 1);
        $collection->setPageSize(3);
        $collection->getSelect()->orderRand();
        $cmsPage = $collection->getFirstItem();
        $urls[$this->getPageTypeName()] = $this->cmsHelper->getPageUrl($cmsPage->getId());
        return $urls;
    }

    public function getPageTypeName(): string
    {
        return 'cms_page';
    }
}
