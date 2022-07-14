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
        foreach ($this->tools as $tool) {
            $tool->execute();
        }
    }
}
