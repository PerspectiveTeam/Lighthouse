<?php

namespace Perspective\Lighthouse\Service;

use Exception;
use Magento\Store\Model\StoreManagerInterface;
use Perspective\Lighthouse\Api\Data\PageTypeToAppendInterface;

class UrlsArrayAppend
{
    /**
     * @var array<mixed>
     */
    private array $data;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array<mixed> $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        array $data = array()
    )
    {
        $this->data = $data;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array<mixed>
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrlsArray()
    {
        $currentStore = $this->storeManager->getStore();
        $stores = $this->storeManager->getStores(false, true);
        $urls = [];
        foreach ($stores as $store) {
            $this->storeManager->setCurrentStore($store->getId());
            foreach ($this->data as $pageTypeToAppend) {
                try {
                    /** @var PageTypeToAppendInterface $pageTypeToAppend */
                    $pageTypeToAppend->setStore($store);
                    $urls = array_merge($urls, $pageTypeToAppend->append($urls));
                } catch (Exception $e) {
                    // do nothing
                }
            }
        }
        $this->storeManager->setCurrentStore($currentStore->getId());
        return array_filter($urls);
    }

}
