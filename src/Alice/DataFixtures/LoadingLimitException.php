<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Alice\DataFixtures;

/**
 * Exception thrown when the number of attempts to load a fixture file has reached a certain limit.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class LoadingLimitException extends \RuntimeException
{
    /**
     * @param int $limit
     * @param array  $normalizedFixturesFiles Array where keys are fixtures files path and value is a boolean set to
     *                                        true for when the fixture file has been loaded and false otherwise.
     */
    public function __construct($limit, array $normalizedFixturesFiles)
    {
        $unloadedFiles = [];

        foreach ($normalizedFixturesFiles as $fileRealPath => $hasBeenLoaded) {
            if (false === $hasBeenLoaded) {
                $unloadedFiles[] = $fileRealPath;
            }
        }

        $this->message = sprintf(
            'Loading files limit of %d reached. Could not load the following files: %s',
            $limit,
            implode(', ', $unloadedFiles)
        );
    }
}
