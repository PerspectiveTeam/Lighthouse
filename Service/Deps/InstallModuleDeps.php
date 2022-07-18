<?php

namespace Perspective\Lighthouse\Service\Deps;

use Magento\Framework\Module\Dir;

class InstallModuleDeps extends AbstractDeps implements \Perspective\Lighthouse\Api\Data\ToolsInterface
{

    public function execute()
    {
        $this->logger->info('Processing module dependencies');
        $moduleRoot = $this->directory->getDir('Perspective_Lighthouse');
        $cmdInstallDeps = $this->prepareNvm() . ' && cd ' . $moduleRoot . ' && nvm run ' . static::NODE_VERSION . ' ~/.nvm/versions/node/' . static::NODE_VERSION . '/bin/npm install';
        $process = $this->runPlainScript($cmdInstallDeps);
        $this->logger->info($process->getErrorOutput() ?: $process->getOutput());
        $this->logger->info('Processed module dependencies');
    }
}
