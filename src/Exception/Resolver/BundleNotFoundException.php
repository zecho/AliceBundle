<?php

namespace Hautelook\AliceBundle\Exception\Resolver;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class BundleNotFoundException extends \RuntimeException
{
    /**
     * @param string            $bundle
     * @param BundleInterface[] $bundles
     * @param int               $code
     * @param \Throwable|null   $previous
     *
     * @return static
     */
    public static function create(string $bundle, array $bundles, int $code = 0, \Throwable $previous = null)
    {
        return new static(
            sprintf(
                'The bundle "%s" was not found. Bundles available are: ["%s"].',
                $bundle,
                implode('", "', array_keys($bundles))
            ),
            $code,
            $previous
        );
    }
}
