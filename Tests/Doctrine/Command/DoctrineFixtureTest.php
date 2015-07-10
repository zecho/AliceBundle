<?php

namespace Hautelook\AliceBundle\Tests\Doctrine\Command;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DoctrineFixtureTest extends KernelTestCase
{
    protected static $class = 'Hautelook\AliceBundle\Tests\SymfonyApp\AppKernel';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var ObjectManager
     */
    private $doctrineManager;

    protected function setUp()
    {
        self::bootKernel();
        $this->application = new Application(self::$kernel);

        // Register doctrine bundles
        $this->application->add(new LoadDataFixturesDoctrineCommand());
        $this->application->add(new CreateSchemaDoctrineCommand());

        $this->application->getKernel()->getContainer()->get('doctrine')->getManager();
        $this->createDatabase();
    }

    public function testFixture()
    {
        $command = $this->application->find('doctrine:fixtures:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(), array('interactive' => false));

        $display = $commandTester->getDisplay();

        $this->assertContains('> purging database', $display);
        $this->assertContains(
            '> loading Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\DataFixtures\ORM\BrandFixtureLoader',
            $display
        );
        $this->assertContains(
            '> loading Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\DataFixtures\ORM\ProductFixtureLoader',
            $display
        );

        $this->verifyProducts();
        $this->verifyBrands();
    }

    /**
     * Generate schema via the doctrine command
     */
    private function createDatabase()
    {
        $command = $this->application->find('doctrine:schema:create');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }

    private function verifyProducts()
    {
        for ($i = 1; $i <= 10; $i++) {
            /** @var $brand \Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Entity\Product */
            $product = $this->doctrineManager->find(
                'Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Entity\Product',
                $i
            );
            $this->assertStringStartsWith('Awesome Product', $product->getDescription());

            // Make sure every product has a brand
            $this->assertInstanceOf(
                'Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Entity\Brand',
                $product->getBrand()
            );
        }
    }

    private function verifyBrands()
    {
        for ($i = 1; $i <= 10; $i++) {
            /** @var $brand \Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Entity\Brand */
            $this->doctrineManager->find(
                'Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Entity\Brand',
                $i
            );
        }
    }
}
