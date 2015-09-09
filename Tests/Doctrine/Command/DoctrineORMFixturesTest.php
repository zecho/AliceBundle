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

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Baldur Rensch <brensch@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class DoctrineORMFixturesTest extends CommandTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp()
    {
        parent::setUp();

        $this->application->add(
            self::$kernel->getContainer()->get('hautelook_alice.doctrine.command.load_command')
        );

        $this->entityManager = $this->application->getKernel()->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @covers \Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader
     */
    public function testFixturesLoading()
    {
        $command = $this->application->find('hautelook_alice:doctrine:fixtures:load');

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
        $command = $this->application->find('hautelook_alice:doctrine:fixtures:load');

        $commandTester = new CommandTester($command);
        $commandTester->execute($inputs, ['interactive' => false]);

        $this->assertEquals(trim($expected,' '), trim($commandTester->getDisplay(), ' '));
    }

    private function verifyProducts()
    {
        for ($i = 1; $i <= 10; ++$i) {
            /* @var \Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Entity\Product */
            $product = $this->entityManager->find(
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
            $this->entityManager->find(
                'Hautelook\AliceBundle\Tests\SymfonyApp\TestBundle\Entity\Brand',
                $i
            );
        }
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

        $data[] = [
            [
                '--env'    => 'ignored',
                '--bundle' => [
                    'TestBundle',
                ]
            ],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml
  > purging database
  > fixtures loaded

EOF
        ];

        $data[] = [
            [
                '--env'    => 'ignored2',
                '--bundle' => [
                    'TestBundle',
                ]
            ],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Ignored2/notIgnored.yml
  > purging database
  > fixtures loaded

EOF
        ];

        $data[] = [
            [
                '--env'    => 'provider',
                '--bundle' => [
                    'TestBundle',
                ]
            ],
            <<<EOF
              > fixtures found:
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/brand.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/product.yml
      - /home/travis/build/theofidry/AliceBundle/Tests/SymfonyApp/TestBundle/DataFixtures/ORM/Provider/testFormatter.yml
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
