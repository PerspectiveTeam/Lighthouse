<?php

namespace Perspective\Lighthouse\Service\Deps;

trait PrepareNvmTrait
{
    /**
     * @return string
     */
    protected function prepareNvm(): string
    {
        return '#!/usr/bin/env bash' . PHP_EOL . ' ' . 'export NVM_DIR=' . trim(shell_exec('echo $NVM_DIR')) . ' && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"';
    }
}
