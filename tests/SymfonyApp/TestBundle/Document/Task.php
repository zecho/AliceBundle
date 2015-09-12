<?php

namespace Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;

/**
 * @PHPCR\Document
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class Task
{
    /**
     * @PHPCR\Id
     */
    public $id;

    /**
     * @PHPCR\String(nullable=true)
     */
    public $description;

    /**
     * @PHPCR\Boolean
     */
    public $done = false;
}
