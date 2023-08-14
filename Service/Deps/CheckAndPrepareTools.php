<?php

namespace Perspective\Lighthouse\Service\Deps;

use Perspective\Lighthouse\Api\Data\ToolsInterface;
use Perspective\Lighthouse\Helper\Logger;
use Perspective\Lighthouse\Helper\Logger\HandlerFactory;

class CheckAndPrepareTools implements ToolsInterface
{
    /**
     * @var array<mixed>
     */
    protected array $tools;

    /**
     * @param array<mixed> $tools
     */
    public function __construct(
        Logger $logger,
        HandlerFactory $handlerFactory,
        array $tools = []
    ) {
        $handler = $handlerFactory->create([
            'root' => '/var/log/lighthouse/',
            'filename' => 'lighthouse_' . date('H:i:s') . '.log'
        ]);
        /**@phpstan-ignore-next-line */
        $this->logger = $logger->pushHandler($handler);
        $this->tools = $tools;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Perspective\Lighthouse\Api\Data\ToolsInterface $tool */
        foreach ($this->tools as $tool) {
            try {
                $tool->execute();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $this->logger->error($e->getTraceAsString());
            }
        }
    }
}
