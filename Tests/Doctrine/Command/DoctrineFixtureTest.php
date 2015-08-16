<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Tests\Doctrine\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Hautelook\AliceBundle\Doctrine\Command\LoadDataFixturesCommand;
use Hautelook\AliceBundle\Tests\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DoctrineFixtureTest extends KernelTestCase
{
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
        $this->application->add(
            new LoadDataFixturesCommand(
                $this->application->getKernel()->getContainer()->get('doctrine'),
                $this->application->getKernel()->getContainer()->get('hautelook_alice.fixtures.loader'),
                $this->application->getKernel()->getContainer()->get('hautelook_alice.doctrine.finder')
            )
        );

        $this->doctrineManager = $this->application->getKernel()->getContainer()->get('doctrine')->getManager();

        $this->application->setAutoExit(false);
        $this->runConsole("doctrine:schema:drop", ["--force" => true]);
        $this->runConsole("doctrine:schema:create");
    }

    public function testFixturesLoading()
    {
        $command = $this->application->find('hautelook_alice:fixtures:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute([], ['interactive' => false]);

        $this->verifyProducts();
        $this->verifyBrands();
    }

    /**
     * @dataProvider loadCommandProvider
     *
     * @param array  $inputs
     * @param string $expected
     */
    public function testFixturesRegistering(array $inputs, $expected)
    {
        $command = $this->application->find('hautelook_alice:fixtures:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute($inputs, ['interactive' => false]);

        $this->assertEquals(trim($expected,' '), trim($commandTester->getDisplay(), ' '));
    }

    private function verifyProducts()
    {
        for ($i = 1; $i <= 10; ++$i) {
            /* @var \Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Entity\Product */
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
        for ($i = 1; $i <= 10; ++$i) {
            /* @var $brand \Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Entity\Brand */
            $this->doctrineManager->find(
                'Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Entity\Brand',
                $i
            );
        }
    }

    private function runConsole($command, array $options = [])
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, ['command' => $command]);
        return $this->application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }

    public function loadCommandProvider()
    {
        $data = [];

        $data[] = [
            [],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml
  > purging database
  > fixtures loaded

EOF
        ];

        $data[] = [
            [
                '--env' => 'dev'
            ],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Dev/dev.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml
  > purging database
  > fixtures loaded

EOF
        ];

        $data[] = [
            [
                '--env' => 'Prod'
            ],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Prod/prod.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml
  > purging database
  > fixtures loaded

EOF
        ];

        $data[] = [
            [
                '--env' => 'prod'
            ],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Prod/prod.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml
  > purging database
  > fixtures loaded

EOF
        ];

        $data[] = [
            [
                '--env'    => 'dev',
                '--bundle' => [
                    'TestBundle'
                ]
            ],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Dev/dev.yml
  > purging database
  > fixtures loaded

EOF
        ];

        $data[] = [
            [
                '--env'    => 'dev',
                '--bundle' => [
                    'TestABundle',
                ]
            ],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml
  > purging database
  > fixtures loaded

EOF
        ];

        $data[] = [
            [
                '--env'    => 'dev',
                '--bundle' => [
                    'TestBundle',
                    'TestABundle',
                ]
            ],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Dev/dev.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml
  > purging database
  > fixtures loaded

EOF
        ];

        $data[] = [
            [
                '--env'    => 'dev',
                '--bundle' => [
                    'TestCBundle',
                ]
            ],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/ABundle/DataFixtures/ORM/aentity.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/Bundle/BBundle/DataFixtures/ORM/bentity.yml
  > purging database
  > fixtures loaded

EOF
        ];

        // Fix paths
        foreach ($data as $index => $dataSet) {
            $data[$index][1] = str_replace('/home/travis/build/theofidry/AliceBundle', getcwd(), $dataSet[1]);
        }

        return $data;
    }
}
