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
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $processors)
    {
        foreach ($processors as $processor) {
            if (false === $processor instanceof ProcessorInterface) {
                throw new \InvalidArgumentException('Expected a Nelmio\Alice\ProcessorInterface instance');
            }
        }

        $this->processors = $processors;
    }

    /**
     * @return array
     */
    public function getProcessors()
    {
        return $this->processors;
    }
}
