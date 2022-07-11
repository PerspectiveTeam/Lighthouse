<?php

namespace Perspective\Lighthouse\Service;

class UrlsArrayAppend
{
    private array $data;

    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    public function getUrlsArray()
    {
        $urls = [];
        foreach ($this->data as $pageTypeToAppend) {
            try {
                $urls = array_merge($urls, $pageTypeToAppend->append($urls));
            } catch (\Exception $e) {
                // do nothing
            }
        }
        return $urls;
    }

}
