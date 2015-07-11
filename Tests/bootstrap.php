<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->loadClass('Hautelook\AliceBundle\Tests\SymfonyApp\AppKernel');

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
