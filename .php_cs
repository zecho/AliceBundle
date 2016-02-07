<?php

require_once __DIR__.'/vendor/sllh/php-cs-fixer-styleci-bridge/autoload.php';

use SLLH\StyleCIBridge\ConfigBridge;
use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;

$header = <<<EOF
This file is part of the Hautelook\AliceBundle package.

(c) Baldur Rensch <brensch@gmail.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

HeaderCommentFixer::setHeader($header);

$config = ConfigBridge::create();

return $config
    ->setUsingCache(true)
    ->fixers(array_merge($config->getFixers(), ['-psr0']))
;
