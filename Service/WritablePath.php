<?php

namespace Perspective\Lighthouse\Service;

class WritablePath
{
    /**
     * @param string $name
     * @param string $url
     * @return string
     */
    public function createWritablePath($name, $url): string
    {
        /**@phpstan-ignore-next-line */
        $newPath = BP . '/var/log/lighthouse/'. $name . '/' . date('Y-m-d') . '/ps_lighthouse_' . urlencode($url) . '_' . date('H:i:s') . '.json';
        mkdir($newPath, 0777, true);
        rmdir($newPath);
        return $newPath;
    }

}
