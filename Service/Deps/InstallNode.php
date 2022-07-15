<?php

namespace Perspective\Lighthouse\Service\Deps;

use Perspective\Lighthouse\Api\Data\ToolsInterface;

class InstallNode extends AbstractDeps implements ToolsInterface
{
    public function execute()
    {
        $this->logger->info('Start download NVM');
        $this->downloadNvm();
        $this->logger->info('NVM downloaded');
        $this->exportToSystem();
        $currentVersionValue = $this->getNodeVersion();
        $this->installSpecifiedNodeVersion(static::NODE_VERSION);
        $this->installSpecifiedNodeVersion($currentVersionValue);
        $this->setNodeVersion($currentVersionValue);
    }

    /**
     * @return void
     */
    protected function downloadNvm(): void
    {
        $cmdInstallNvm[] = 'curl';
        $cmdInstallNvm[] = '-o-';
        $cmdInstallNvm[] = 'https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh';
        $scriptContent = $this->runCli($cmdInstallNvm, 10)->getOutput();
        $this->logger->info('Init script downloaded. Executing the script');
        $this->runPlainScript($scriptContent);
    }

    /**
     * @return void
     */
    protected function exportToSystem(): void
    {
        $this->logger->info('Exporting NVM to system');
        $cmdExportNvm = 'export NVM_DIR="$([ -z "${XDG_CONFIG_HOME-}" ] && printf %s "${HOME}/.nvm" || printf %s "${XDG_CONFIG_HOME}/nvm")"';
        $this->runPlainScript('#!/bin/bash' . PHP_EOL . ' ' . $cmdExportNvm, 10);
        $this->runPlainScript('#!/bin/zsh' . PHP_EOL . ' ' . $cmdExportNvm, 10);
        $this->runPlainScript('#!/bin/ksh' . PHP_EOL . ' ' . $cmdExportNvm, 10);
        $this->runPlainScript('#!/bin/bash' . PHP_EOL . ' ' . ' source ~/.bashrc', 5);
        $this->runPlainScript('#!/bin/zsh' . PHP_EOL . ' ' . 'source ~/.zshrc', 5);
        $this->runPlainScript('#!/bin/ksh' . PHP_EOL . ' ' . '. ~/.profile', 5);
    }

    /**
     * @return string
     */
    public function getNodeVersion(): string
    {
        $cmdGetNodeVersion = $this->prepareNvm() . ' && nvm current';
        $nodeVersion = trim($this->runPlainScript($cmdGetNodeVersion)->getOutput());
        $this->logger->info('Current Node Version: ' . $nodeVersion);
        return $nodeVersion;

    }


    /**
     * @return void
     */
    protected function installSpecifiedNodeVersion($version): void
    {
        if (!file_exists(getenv('HOME') . '/.bash_profile')) {
            shell_exec('touch ~/.bash_profile');
        }
        $this->logger->info('Installing Node Version: ' . $version);
        $scriptString = $this->prepareNvm() . ' && nvm install ' . $version;
        $process = $this->runPlainScript($scriptString);
        if ($process->getExitCode() !== 0) {
            $this->logger->error('Error while installing Node Version: ' . $version);
            $this->logger->error($process->getErrorOutput(), ['errorOutput']);
            $this->logger->error($process->getOutput(), ['regularOutput']);
        } else {
            $this->logger->info('Node Version: ' . $version . ' installed');
        }
    }

    /**
     * @param string $currentVersionValue
     * @return void
     */
    public function setNodeVersion(string $versionValue): void
    {
        $this->logger->info('Using Node Version: ' . $versionValue);
        $scriptString = $this->prepareNvm() . ' && nvm use ' . $versionValue;
        $this->runPlainScript($scriptString);
    }
}
