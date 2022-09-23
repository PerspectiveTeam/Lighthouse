<?php

namespace Perspective\Lighthouse\Api\Data;

interface PageTypeToAppendInterface
{
    /**
     * @return string
     */
    public function getPageTypeName(): string;

    /**
     * @param array $urls
     * @return array
     */
    public function append(array $urls): array;

    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return void
     */
    public function setStore($store): void;

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore();
}
