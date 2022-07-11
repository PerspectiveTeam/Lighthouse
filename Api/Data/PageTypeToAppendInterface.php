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
}
