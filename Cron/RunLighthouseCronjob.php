<?php

namespace Perspective\Lighthouse\Cron;

use Dzava\Lighthouse\Exceptions\AuditFailedException;
use Dzava\Lighthouse\LighthouseFactory;
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

    /**
     * @param \Dzava\Lighthouse\LighthouseFactory $lighthouseFactory
     * @param \Perspective\Lighthouse\Service\UrlsArrayAppend $urlsArrayAppend
     * @param \Perspective\Lighthouse\Service\WritablePath $writablePath
     */
    public function __construct(
        LighthouseFactory $lighthouseFactory,
        UrlsArrayAppend $urlsArrayAppend,
        WritablePath $writablePath,
        Logger $logger,
        HandlerFactory $handlerFactory
    ) {
        $this->lighthouseFactory = $lighthouseFactory;
        $this->urlsArrayAppend = $urlsArrayAppend;
        $this->writablePath = $writablePath;
        $handler = $handlerFactory->create([
            'root' => '/var/log/lighthouse/',
            'filename' => 'lighthouse_' . date('H:i:s') . '.log'
        ]);
        $this->logger = $logger->pushHandler($handler);
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
        $this->logger->info('Node version:' . exec('node -v'));
        $this->logger->info('Try to use v16.15.1 version of node if you get error or coredumped');
        $lighthouse
            ->setLighthousePath(BP . '/app/code/Perspective/Lighthouse/node_modules/lighthouse/lighthouse-cli/index.js')
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
                $lighthouse->audit($url);
            } catch (AuditFailedException $e) {
                echo $e->getOutput();
                $this->logger->info($e->getOutput());
            }
        }
    }
}
