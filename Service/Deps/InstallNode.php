<?php

namespace Perspective\Lighthouse\Service\Deps;

use Perspective\Lighthouse\Api\Data\ToolsInterface;

class InstallNode extends AbstractDeps implements ToolsInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->logger->info('Start download NVM');
        $this->downloadNvm();
        $this->logger->info('NVM downloaded');
        $this->exportToSystem();
        $currentVersionValue = $this->getNodeVersion();
        $this->installSpecifiedNodeVersion(static::NODE_VERSION);
        $this->installSpecifiedNodeVersion($currentVersionValue);
    }

    /**
     * @return void
     */
    protected function downloadNvm(): void
    {
        $cmdInstallNvm[] = 'curl';
        $cmdInstallNvm[] = '-o-';
        $cmdInstallNvm[] = 'https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh';
        $scriptContent = $this->runCli($cmdInstallNvm, 180)->getOutput();
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
        $shell = trim(shell_exec('echo $0') ?: '/bin/sh');
        $this->runPlainScript('#!/usr/bin/env' . $shell . PHP_EOL . ' ' . $cmdExportNvm, 10);
        if (strlen(trim(shell_exec('echo $NVM_DIR') ?: '')) === 0) {
            // if export failed, try to fallback
            $this->runPlainScript($this->prepareNvm());
        }
        switch ($shell) {
            case 'bash':
                $this->runPlainScript('#!/usr/bin/env' . $shell . PHP_EOL . ' ' . ' source ~/.bashrc', 5);
                break;
            case 'sh':
                $this->runPlainScript('#!/usr/bin/env' . $shell . PHP_EOL . ' ' . ' source ~/.bashrc', 5);
                break;
            case 'zsh':
                $this->runPlainScript('#!/usr/bin/env' . $shell . PHP_EOL . ' ' . 'source ~/.zshrc', 5);
                break;
            case 'ksh':
                $this->runPlainScript('#!/usr/bin/env' . $shell . PHP_EOL . ' ' . '. ~/.profile', 5);
                break;
            default:
                //just try to invoke all
                $this->runPlainScript('#!/usr/bin/env' . $shell . PHP_EOL . ' ' . ' source ~/.bashrc', 5);
                $this->runPlainScript('#!/usr/bin/env' . $shell . PHP_EOL . ' ' . ' source ~/.bashrc', 5);
                $this->runPlainScript('#!/usr/bin/env' . $shell . PHP_EOL . ' ' . 'source ~/.zshrc', 5);
                $this->runPlainScript('#!/usr/bin/env' . $shell . PHP_EOL . ' ' . '. ~/.profile', 5);
                break;
        }
    }

    /**
     * I think also need deprecate like
     * @see setNodeVersion
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
     * @param string $version
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
     * @deprecated
     * cause nvm runs as function of current shell
     * so it dies in the end of command execution
     * @param string $versionValue
     * @return void
     */
    public function setNodeVersion(string $versionValue): void
    {
        $this->logger->info('Using Node Version: ' . $versionValue);
        $scriptString = $this->prepareNvm() . ' && nvm use ' . $versionValue;
        $this->runPlainScript($scriptString);
    }
}
