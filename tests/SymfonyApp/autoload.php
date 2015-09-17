<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver as ODMAnnotationDriver;

/** @var ClassLoader $loader */
$loader = require __DIR__.'/../../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);
ODMAnnotationDriver::registerAnnotationClasses();

AnnotationRegistry::registerLoader([$loader, 'loadClass']);
AnnotationRegistry::registerFile(__DIR__.'/../../vendor/doctrine/phpcr-odm/lib/Doctrine/ODM/PHPCR/Mapping/Annotations/DoctrineAnnotations.php');

return $loader;
