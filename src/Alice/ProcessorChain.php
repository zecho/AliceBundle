<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Alice;

use Nelmio\Alice\ProcessorInterface;

/**
 * Calls multiple {@see Nelmio\Alice\ProcessorInterface} instances in a chain.
 *
 * This class accepts multiple instances of ProcessorInterface to be passed to the constructor.
 *
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
