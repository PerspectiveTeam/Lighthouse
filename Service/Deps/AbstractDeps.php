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

    private $envsProcessed = false;

    /**
     * @var \Perspective\Lighthouse\Helper\Logger
     */
    protected Logger $logger;

    /**
     * @param \Magento\Framework\Module\Dir $directory
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
        $this->logger = $logger->pushHandler($handler);
        $this->directory = $directory;
        $this->executableFinder = $executableFinder;
    }

    public function runCli(array $command, int $timeout = 60, $callback = null)
    {
        $this->setEnvs();
        $symphonyProcess = new Process(
            $command,
            $this->directory->getDir('Perspective_Lighthouse'),
            explode(':', getenv('PATH'))
        );
        $symphonyProcess->setTimeout($timeout)->run($callback);
        return $symphonyProcess;
    }

    /**
     * @return void
     */
    protected function setEnvs(): void
    {
        if (!$this->envsProcessed) {
            putenv('PATH=' . getenv('PATH') . ':' . trim(shell_exec('echo $NVM_DIR')));
            $this->envsProcessed = true;
        }
    }

    public function runPlainScript($command, int $timeout = 60, $callback = null)
    {
        $this->setEnvs();
        $symphonyProcess = Process::fromShellCommandline(
            $command,
            $this->directory->getDir('Perspective_Lighthouse'),
            explode(':', getenv('PATH'))
        );
        $symphonyProcess->setTimeout($timeout)->run($callback);
        return $symphonyProcess;
    }

}