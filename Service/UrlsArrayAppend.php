<?php

namespace Perspective\Lighthouse\Service;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Cms\Helper\Page;
use Magento\Framework\UrlInterface;

class UrlsArrayAppend
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private UrlInterface $url;

    private CollectionFactory $productCollectionFactory;

    private \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory;

    private \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $cmsCollectionFactory;

    private Page $cmsHelper;

    /**
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $cmsCollectionFactory
     * @param \Magento\Cms\Helper\Page $cmsHelper
     */
    public function __construct(
        UrlInterface $url,
        CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $cmsCollectionFactory,
        Page $cmsHelper
    ) {
        $this->url = $url;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->cmsCollectionFactory = $cmsCollectionFactory;
        $this->cmsHelper = $cmsHelper;
    }

    public function getUrlsArray()
    {
        $urls = [];
        $this->appendWithHomeUrlsArray($urls);
        $this->appendWithRandomProductPage($urls);
        $this->appendWithRandomCategoryPage($urls);
        $this->appendWithRandomCMS($urls);
        return $urls;
    }

    protected function appendWithHomeUrlsArray(&$urls)
    {
        $urls['home'] = $this->url->getBaseUrl();
    }

    protected function appendWithRandomProductPage(&$urls)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter('status', 1);
        $collection->addFieldToFilter('visibility', ['in' => [2, 3, 4]]);
        $collection->addAttributeToSelect('*')
            ->setPageSize(3);
        $collection->getSelect()->orderRand();
        $product = $collection->getFirstItem();
        $urls['product'] = $product->getProductUrl();
    }

    protected function appendWithRandomCategoryPage(&$urls)
    {
        $collection = $this->categoryFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->addAttributeToSelect('*')
            ->setPageSize(3);
        $collection->getSelect()->orderRand();
        $category = $collection->getFirstItem();
        $urls['category'] = $category->getUrl();
    }

    protected function appendWithRandomCMS(&$urls)
    {
        $collection = $this->cmsCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->setPageSize(3);
        $collection->getSelect()->orderRand();
        $cmsPage = $collection->getFirstItem();
        $urls['cms_page'] = $this->cmsHelper->getPageUrl($cmsPage->getId());
    }
}
