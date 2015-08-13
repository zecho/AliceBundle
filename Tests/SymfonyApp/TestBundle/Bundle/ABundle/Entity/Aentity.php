<?php

namespace Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Bundle\ABundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Aentity
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }
}
