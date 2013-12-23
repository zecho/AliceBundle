<?php

namespace Hautelook\AliceBundle\Tests\Functional\Command;

use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Hautelook\AliceBundle\Tests\Functional\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\Shell\Command;

class DoctrineFixtureTest extends TestCase
{
    public function testFixture()
    {
        $application = new Application(self::getKernel());
        $application->add(new LoadDataFixturesDoctrineCommand());
        $command = $application->find('doctrine:fixtures:load');
//        $command = new \Symfony\Component\Console\Command\Command();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array());

        $display = $commandTester->getDisplay();

        var_dump($display);
    }
} 
