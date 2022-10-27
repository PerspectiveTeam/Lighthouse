<?php

namespace Perspective\Lighthouse\Service\Deps;

use Perspective\Lighthouse\Api\Data\ToolsInterface;

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
        array $tools = []
    ) {
        $this->tools = $tools;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Perspective\Lighthouse\Api\Data\ToolsInterface $tool */
        foreach ($this->tools as $tool) {
            $tool->execute();
        }
    }
}
