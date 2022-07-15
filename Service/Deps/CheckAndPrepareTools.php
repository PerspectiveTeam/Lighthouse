<?php

namespace Perspective\Lighthouse\Service\Deps;

class CheckAndPrepareTools
{
    protected array $tools;

    public function __construct(
        array $tools = []
    ) {
        $this->tools = $tools;
    }

    public function execute()
    {
        /** @var \Perspective\Lighthouse\Api\Data\ToolsInterface $tool */
        foreach ($this->tools as $tool) {
            $tool->execute();
        }
    }
}
