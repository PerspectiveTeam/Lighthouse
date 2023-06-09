<?php

namespace Perspective\Lighthouse\Cron;

use Dzava\Lighthouse\Exceptions\AuditFailedException;
use Dzava\Lighthouse\LighthouseFactory;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Dir;
use Perspective\Lighthouse\Helper\Logger;
use Perspective\Lighthouse\Helper\Logger\HandlerFactory;
use Perspective\Lighthouse\Service\Deps\CheckAndPrepareTools;
use Perspective\Lighthouse\Service\Deps\InstallNode;
use Perspective\Lighthouse\Service\Deps\PrepareNvmTrait;
use Perspective\Lighthouse\Service\UrlsArrayAppend;
use Perspective\Lighthouse\Service\WritablePath;

class RunLighthouseCronjob
{
    use PrepareNvmTrait;

    private LighthouseFactory $lighthouseFactory;

    /**
     * @var \Perspective\Lighthouse\Service\UrlsArrayAppend
     */
    private UrlsArrayAppend $urlsArrayAppend;

    private WritablePath $writablePath;

    /**
     * @var \Perspective\Lighthouse\Helper\Logger
     */
    private Logger $logger;

    private ScopeConfigInterface $scopeConfig;

    private Dir $directory;

    private CheckAndPrepareTools $checkAndPrepareTools;

    private InstallNode $manageNode;

    /**
     * @param \Dzava\Lighthouse\LighthouseFactory $lighthouseFactory
     * @param \Perspective\Lighthouse\Service\UrlsArrayAppend $urlsArrayAppend
     * @param \Perspective\Lighthouse\Service\WritablePath $writablePath
     * @param \Perspective\Lighthouse\Helper\Logger $logger
     * @param \Perspective\Lighthouse\Helper\Logger\HandlerFactory $handlerFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir $directory
     * @param \Perspective\Lighthouse\Service\Deps\CheckAndPrepareTools $checkAndPrepareTools
     * @param \Perspective\Lighthouse\Service\Deps\InstallNode $manageNode
     */
    public function __construct(
        LighthouseFactory $lighthouseFactory,
        UrlsArrayAppend $urlsArrayAppend,
        WritablePath $writablePath,
        Logger $logger,
        HandlerFactory $handlerFactory,
        ScopeConfigInterface $scopeConfig,
        Dir $directory,
        CheckAndPrepareTools $checkAndPrepareTools,
        InstallNode $manageNode
    ) {
        $this->lighthouseFactory = $lighthouseFactory;
        $this->urlsArrayAppend = $urlsArrayAppend;
        $this->writablePath = $writablePath;
        $handler = $handlerFactory->create([
            'root' => '/var/log/lighthouse/',
            'filename' => 'lighthouse_' . date('H:i:s') . '.log'
        ]);
        /**@phpstan-ignore-next-line */
        $this->logger = $logger->pushHandler($handler);
        $this->scopeConfig = $scopeConfig;
        $this->directory = $directory;
        $this->checkAndPrepareTools = $checkAndPrepareTools;
        $this->manageNode = $manageNode;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(): void
    {
        try {
            $this->logger->info('Start toolkit checking');
            $this->checkAndPrepareTools->execute();
            $this->logger->info('Toolkit checked without errors');
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            $this->logger->info('One of the tools is not installed or not configured properly');
        }
        /** @var \Dzava\Lighthouse\Lighthouse $lighthouse */
        $lighthouse = $this->lighthouseFactory->create();
        //try to use v16.15.1 version of node if you get error or coredumped
        $pathForLighthouseCli = $this->getPathForLighthouseCli();
        $nodePath = $this->getNodePath();
        $lighthouse
            ->setLighthousePath($pathForLighthouseCli)
            ->setNodePath($nodePath)
            ->accessibility()
            ->bestPractices()
            ->performance()
            ->pwa()
            ->seo()
            ->setChromeFlags(["--ignore-certificate-errors", '--headless', '--disable-gpu', '--no-sandbox', "--enable-logging"]);
        $urls = $this->urlsArrayAppend->getUrlsArray();
        $this->auditGivenUrls($urls, $lighthouse);
    }

    /**
     * @return mixed|string
     */
    protected function getNodePath()
    {
        $nodePathArr = explode(PHP_EOL, trim($this->manageNode->runPlainScript($this->prepareNvm() . ' && nvm use ' . $this->manageNode::NODE_VERSION . ' && nvm which current')->getOutput()));
        // always on last position
        $nodePath = end($nodePathArr);
        if (empty($nodePath) || !file_exists($nodePath)) {
            $this->logger->error('Node not found. Trying fallback Node');
            $nodePath = $this->scopeConfig->getValue('lighthouse/schedule_group/node_path');
            if (strpos($nodePath, '~') !== false) {
                $nodePath = getenv('HOME') . ltrim($nodePath, '~');
            }
            //check if node path is exist
            if (empty($nodePath)) {
                //if not present log message and shutdown cronjob
                $this->logger->info('Node path is not set. Please set it in the configuration.');
                $nodePath = null;
            }
        }
        return $nodePath;
    }

    /**
     * @param array<mixed> $urls
     * @param \Dzava\Lighthouse\Lighthouse $lighthouse
     * @return void
     */
    protected function auditGivenUrls(array $urls, \Dzava\Lighthouse\Lighthouse $lighthouse): void
    {
        foreach ($urls as $name => $url) {
            try {
                $newPath = $this->writablePath->createWritablePath($name, $url);
                $lighthouse->setOutput($newPath, ['json', 'html']);
                $this->logger->info('Lighthouse audit for ' . $url . ' is started.');
                $this->logger->info("Lighthouse command was:\n" . implode(' ', $lighthouse->getCommand($url)));
                $lighthouse->audit($url);
                $this->logger->info('Lighthouse audit for ' . $url . ' is done');
            } catch (AuditFailedException $e) {
                echo $e->getOutput();
                $this->logger->info($e->getOutput());
            }
        }
    }

    /**
     * @return string
     */
    protected function getPathForLighthouseCli(): string
    {
        return $this->directory->getDir('Perspective_Lighthouse') . '/node_modules/lighthouse/lighthouse-cli/index.js';
    }
}
