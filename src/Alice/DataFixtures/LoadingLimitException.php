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
     * @param int   $limit
     * @param array $normalizedFixturesFiles Array where keys are fixtures files path and value is a boolean set to
     *                                       true for when the fixture file has been loaded and false otherwise.
     * @param array $errorMessages           All encountered errors messages while trying to load fixtures.
     */
    public function __construct($limit, array $normalizedFixturesFiles, array $errorMessages = [])
    {
        $unloadedFiles = [];

        foreach ($normalizedFixturesFiles as $fileRealPath => $hasBeenLoaded) {
            if (false === $hasBeenLoaded) {
                $unloadedFiles[] = $fileRealPath;
            }
        }

        $messageLines = [];
        $messageLines[] = sprintf('Loading files limit of %d reached. Could not load the following files:', $limit);

        sort($unloadedFiles);
        foreach ($unloadedFiles as $unloadedFile) {
            if (isset($errorMessages[$unloadedFile]) && 0 < count($errorMessages[$unloadedFile])) {
                $messageLines[] = sprintf('%s:', $unloadedFile);
                $fileErrorMessages = array_unique($errorMessages[$unloadedFile]);
                foreach ($fileErrorMessages as $errorMessage) {
                    $messageLines[] = sprintf(' - %s', $errorMessage);
                }
            } else {
                $messageLines[] = $unloadedFile;
            }
        }

        $this->message = implode(PHP_EOL, $messageLines);
    }
}
