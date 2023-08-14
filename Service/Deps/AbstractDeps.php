<?php

namespace Perspective\Lighthouse\Service\Deps;

use Magento\Framework\Module\Dir;
use Perspective\Lighthouse\Helper\Logger;
use Perspective\Lighthouse\Helper\Logger\HandlerFactory;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class AbstractDeps
{
    use \Perspective\Lighthouse\Service\Deps\PrepareNvmTrait;
    public const NODE_VERSION = 'v16.15.1';

    /**
     * @var \Magento\Framework\Module\Dir
     */
    protected Dir $directory;

    protected ExecutableFinder $executableFinder;


    /**
     * @var \Perspective\Lighthouse\Helper\Logger
     */
    protected Logger $logger;

    /**
     * @param \Magento\Framework\Module\Dir $directory
     * @param \Symfony\Component\Process\ExecutableFinder $executableFinder
     * @param \Perspective\Lighthouse\Helper\Logger $logger
     * @param \Perspective\Lighthouse\Helper\Logger\HandlerFactory $handlerFactory
     */
    public function __construct(
        Dir $directory,
        ExecutableFinder $executableFinder,
        Logger $logger,
        HandlerFactory $handlerFactory
    ) {
        $handler = $handlerFactory->create([
            'root' => '/var/log/lighthouse/',
            'filename' => 'lighthouse_' . date('H:i:s') . '.log'
        ]);
        /**@phpstan-ignore-next-line */
        $this->logger = $logger->pushHandler($handler);
        $this->directory = $directory;
        $this->executableFinder = $executableFinder;
    }

    /**
     * @param array<mixed> $command
     * @param int $timeout
     * @param callable|null $callback
     * @return \Symfony\Component\Process\Process<mixed>
     */
    public function runCli(array $command, int $timeout = 300, $callback = null)
    {
        $this->processEnvs();
        $symphonyProcess = new Process(
            $command,
            $this->directory->getDir('Perspective_Lighthouse'),
            explode(':', (string)getenv('PATH'))
        );
        if (!$callback) {
            $callback = function ($type, $buffer) {
                $this->logger->info($buffer);
            };
        }
        $symphonyProcess->setTimeout($timeout)->run($callback);
        return $symphonyProcess;
    }

    /**
     * @return void
     */
    protected function processEnvs(): void
    {
        $envArray[] = trim(shell_exec('echo $NVM_DIR') ?: '');
        $envArray[] = getenv('HOME') . '/.nvm';
        $envArray = array_filter($envArray);
        $nvmDirEnv = ':' . implode(':', $envArray);
        if (strlen($nvmDirEnv) > 1) {
            putenv('PATH=' . getenv('PATH') . $nvmDirEnv);
        }
    }

    /**
     * @param string $command
     * @param int $timeout
     * @param callable|null $callback
     * @return \Symfony\Component\Process\Process<mixed>
     */
    public function runPlainScript($command, int $timeout = 300, $callback = null)
    {
        $this->processEnvs();
// bash -c робе без -с не робе - висне
        $symphonyProcess = Process::fromShellCommandline(
            'bash "' . $command . '"',
            $this->directory->getDir('Perspective_Lighthouse'),
            explode(':', (string)getenv('PATH'))
        );
        if (!$callback) {
            $callback = function ($type, $buffer) {
                $this->logger->info($buffer);
            };
        }
        $symphonyProcess->setTimeout($timeout)->run($callback);
        return $symphonyProcess;
    }

}
