<?php

namespace Perspective\Lighthouse\Service\Deps;

trait PrepareNvmTrait
{
    /**
     * @return string
     */
    protected function prepareNvm(): string
    {
        $shell = trim(shell_exec('echo $0'));
        $nvmDir = trim(shell_exec('echo ${NVM_DIR}'));
        if ($nvmDir === '') {
            // just try to fallback
            $nvmDir = getenv('HOME') . '/.nvm';
        }
        return '#!/usr/bin/env ' . $shell . PHP_EOL . ' ' . 'export NVM_DIR=' . $nvmDir . ' && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"';
    }
}
