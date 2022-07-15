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
        $scriptContent = $this->runCli($cmdInstallChromium, 30)->getOutput();
        @unlink(getenv('HOME') . '/bin/chrome');
        mkdir(getenv('HOME') . '/bin/chrome', 0777, true);
        rmdir(getenv('HOME') . '/bin/chrome');
        $scriptContent = $scriptContent . PHP_EOL . 'rm -f $ZIP_FILE';
        $this->runPlainScript($scriptContent, 180);
        $this->runPlainScript('ln -sf ' . $this->directory->getDir('Perspective_Lighthouse') . '/latest/chrome ' . getenv('HOME') . '/bin/chrome');
        $this->logger->info('Installed Chrome executable');
    }
}
