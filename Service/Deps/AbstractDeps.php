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
     * @var bool
     */
    private $envsProcessed = false;

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
    public function runCli(array $command, int $timeout = 60, $callback = null)
    {
        $this->processEnvs();
        $symphonyProcess = new Process(
            $command,
            $this->directory->getDir('Perspective_Lighthouse'),
            explode(':', (string)getenv('PATH'))
        );
        $symphonyProcess->setTimeout($timeout)->run($callback);
        return $symphonyProcess;
    }

    /**
     * @return void
     */
    protected function processEnvs(): void
    {
        if (!$this->envsProcessed) {
            $envArray[] = trim(shell_exec('echo $NVM_DIR')?:'');
            $envArray[] = getenv('HOME') . '/bin';
            $envArray = array_filter($envArray);
            $nvmDirEnv = ':' . implode(':', $envArray);
            if (strlen($nvmDirEnv) > 1) {
                putenv('PATH=' . getenv('PATH') . $nvmDirEnv);
            }
            $this->envsProcessed = true;
        }
    }

    /**
     * @param string $command
     * @param int $timeout
     * @param callable|null $callback
     * @return \Symfony\Component\Process\Process<mixed>
     */
    public function runPlainScript($command, int $timeout = 60, $callback = null)
    {
        $this->processEnvs();
        $symphonyProcess = Process::fromShellCommandline(
            $command,
            $this->directory->getDir('Perspective_Lighthouse'),
            explode(':', (string)getenv('PATH'))
        );
        $symphonyProcess->setTimeout($timeout)->run($callback);
        return $symphonyProcess;
    }

}
