<?php

namespace Hautelook\AliceBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as SymfonyKernelTestCase;

/**
 * Overrides the $class property as {@see SymfonyKernelTestCase::getKernelClass()} does not seems to resolve
 * properly the AppKernel class.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class KernelTestCase extends SymfonyKernelTestCase
{
    protected static $class = 'Hautelook\AliceBundle\Tests\SymfonyApp\AppKernel';
}
