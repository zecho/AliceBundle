<?php

namespace Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class Product
{
    /**
     * @MongoDB\Id
     */
    public $id;

    /**
     * @MongoDB\String
     */
    public $name;

    /**
     * @MongoDB\Float
     */
    public $price;
}
