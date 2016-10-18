<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle;

use Psr\Log\LoggerInterface;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
interface LoggerAwareInterface
{
    /**
     * @param LoggerInterface $logger
     *
     * @return static
     */
    public function withLogger(LoggerInterface $logger);
}
