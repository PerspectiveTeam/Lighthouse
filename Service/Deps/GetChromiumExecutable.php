<?php


namespace Perspective\Lighthouse\Service\Deps;

class GetChromiumExecutable extends AbstractDeps implements \Perspective\Lighthouse\Api\Data\ToolsInterface

{

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->logger->info('Installing chromium executable');
        $cmdInstallChromium[] = 'curl';
        $cmdInstallChromium[] = '-o-';
        $cmdInstallChromium[] = 'https://raw.githubusercontent.com/scheib/chromium-latest-linux/master/update.sh';
        $scriptContent = $this->runCli($cmdInstallChromium, 180)->getOutput();
        $this->runPlainScript($scriptContent);
        $this->logger->info('Installed Chrome executable');
    }
}
