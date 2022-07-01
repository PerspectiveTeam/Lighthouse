<?php

namespace Perspective\Lighthouse\Helper;

/**
 * Class Logger
 */
class Logger extends \Monolog\Logger{
    public function __construct($name = 'generalLogger', array $handlers = array(), array $processors = array())
    {
        parent::__construct($name, $handlers, $processors);
    }
}
