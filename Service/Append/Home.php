<?php

namespace Perspective\Lighthouse\Service\Append;

use Magento\Framework\UrlInterface;
use Perspective\Lighthouse\Api\Data\PageTypeToAppendInterface;

class Home implements PageTypeToAppendInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private UrlInterface $url;

    /**
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    public function append(array $urls): array
    {
        $urls[$this->getPageTypeName()] = $this->url->getBaseUrl();
        return $urls;
    }

    public function getPageTypeName(): string
    {
        return 'home';
    }
}
