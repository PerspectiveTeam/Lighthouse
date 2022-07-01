<?php

namespace Perspective\Lighthouse\Cron;

use Dzava\Lighthouse\Exceptions\AuditFailedException;
use Dzava\Lighthouse\LighthouseFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Dir;
use Perspective\Lighthouse\Service\UrlsArrayAppend;
use Perspective\Lighthouse\Service\WritablePath;
use Perspective\Lighthouse\Helper\Logger;
use Perspective\Lighthouse\Helper\Logger\HandlerFactory;

class RunLighthouseCronjob
{
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

    /**
     * @param \Dzava\Lighthouse\LighthouseFactory $lighthouseFactory
     * @param \Perspective\Lighthouse\Service\UrlsArrayAppend $urlsArrayAppend
     * @param \Perspective\Lighthouse\Service\WritablePath $writablePath
     * @param \Perspective\Lighthouse\Helper\Logger $logger
     * @param \Perspective\Lighthouse\Helper\Logger\HandlerFactory $handlerFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir $directory
     */
    public function __construct(
        LighthouseFactory $lighthouseFactory,
        UrlsArrayAppend $urlsArrayAppend,
        WritablePath $writablePath,
        Logger $logger,
        HandlerFactory $handlerFactory,
        ScopeConfigInterface $scopeConfig,
        Dir $directory
    ) {
        $this->lighthouseFactory = $lighthouseFactory;
        $this->urlsArrayAppend = $urlsArrayAppend;
        $this->writablePath = $writablePath;
        $handler = $handlerFactory->create([
            'root' => '/var/log/lighthouse/',
            'filename' => 'lighthouse_' . date('H:i:s') . '.log'
        ]);
        $this->logger = $logger->pushHandler($handler);
        $this->scopeConfig = $scopeConfig;
        $this->directory = $directory;
    }

    /**
     * Cronjob Description
     *
     * @return void
     */
    public function execute(): void
    {
        $lighthouse = $this->lighthouseFactory->create();
        //try to use v16.15.1 version of node if you get error or coredumped
        $this->logger->info('Node version:' . $this->scopeConfig->getValue('lighthouse/general/node_path') ?? 'absent version! May works.');
        $this->logger->info('Try to use v16.15.1 version of node if you get error or coredumped.');
        $pathForLighthouseCli = $this->directory->getDir( 'Perspective_Lighthouse') . '/node_modules/lighthouse/lighthouse-cli/index.js';
        $lighthouse
            ->setLighthousePath($pathForLighthouseCli)
            ->setNodePath($this->scopeConfig->getValue('lighthouse/schedule_group/node_path'))
            ->accessibility()
            ->bestPractices()
            ->performance()
            ->pwa()
            ->seo()
            ->setChromeFlags(["--ignore-certificate-errors", '--headless', '--disable-gpu', '--no-sandbox']);
        $urls = $this->urlsArrayAppend->getUrlsArray();
        foreach ($urls as $name => $url) {
            try {
                $newPath = $this->writablePath->createWritablePath($name, $url);
                $lighthouse->setOutput($newPath, ['json', 'html']);
                $this->logger->info('Lighthouse audit for ' . $url . ' is started.');
                $lighthouse->audit($url);
                $this->logger->info('Lighthouse audit for ' . $url . ' is done');
            } catch (AuditFailedException $e) {
                echo $e->getOutput();
                $this->logger->info($e->getOutput());
            }
        }
    }
}
