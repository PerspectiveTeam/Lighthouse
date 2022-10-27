<?php

namespace Perspective\Lighthouse\Helper;

/**
 * Class Logger
 */
class Logger extends \Monolog\Logger
{
    /**
     * @param string $name
     * @param array<mixed> $handlers
     * @param array<mixed> $processors
     */
    public function __construct(
        $name = 'generalLogger',
        array $handlers = array(),
        array $processors = array()
    ) {
        parent::__construct($name, $handlers, $processors);
    }
}
