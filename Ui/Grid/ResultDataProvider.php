<?php

namespace Perspective\Lighthouse\Ui\Grid;

use Hyva\Admin\Api\HyvaGridArrayProviderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\FileFactory;

class ResultDataProvider implements HyvaGridArrayProviderInterface
{

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private DirectoryList $directoryList;

    public function __construct(DirectoryList $directoryList, FileFactory $fileFactory)
    {
        $this->directoryList = $directoryList;
        $this->fileFactory = $fileFactory;
    }

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
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                    'path' => base64_encode($file),
                    'url' => urldecode($matchesUrl[1]) ?? $file,
                    'type' => $matchesType[1] ?? 'unknown'
                ];
            }
        }
        return $results;
    }
}
