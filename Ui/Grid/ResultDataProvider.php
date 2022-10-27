<?php

namespace Perspective\Lighthouse\Ui\Grid;

use Hyva\Admin\Api\HyvaGridArrayProviderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ResultDataProvider implements HyvaGridArrayProviderInterface
{

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private DirectoryList $directoryList;

    public function __construct(DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    /**
     * @return array<mixed>
     */
    public function getHyvaGridData(): array
    {
        $results = [];
        $dirPath = $this->directoryList->getPath(DirectoryList::LOG) . '/lighthouse/*/*/*.html';
        $files = \glob($dirPath, GLOB_NOSORT);
        if (!empty($files)) {
            $i = 1;
            $regexType = '/lighthouse\/(.*)\/\d/m';
            $regexUrl = '/ps_lighthouse_(.*)\_/m';
            foreach ($files as $file) {
                preg_match($regexType, $file, $matchesType);
                preg_match($regexUrl, $file, $matchesUrl);
                $results[] = [
                    'id' => $i++,
                    'date' => date('Y-m-d H:i:s', (int)filemtime($file)),
                    'path' => base64_encode($file),
                    /**@phpstan-ignore-next-line */
                    'url' => urldecode($matchesUrl[1]) ?? $file,
                    'type' => $matchesType[1] ?? 'unknown'
                ];
            }
        } else {
            $results[] = [
                'id' => null,
                'date' => null,
                'path' => base64_encode('empty'),
                'url' => null,
                'type' => null
            ];
        }
        return $results;
    }
}
