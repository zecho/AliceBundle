<?php

namespace Hautelook\AliceBundle\Tests\Functional\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RootController extends Controller
{
    public function testAction()
    {
        return new Response("TestResponse");
    }
}
