<?php

namespace Hautelook\AliceBundle\Alice;

use Nelmio\Alice\ProcessorInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class ProcessorChain
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors = [];

    /**
     * @param ProcessorInterface $processor
     */
    public function addProcessor(ProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    /**
     * @return array
     */
    public function getProcessors()
    {
        return $this->processors;
    }
}
