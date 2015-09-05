<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hautelook\AliceBundle\Doctrine\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\DataFixtures\Purger\MongoDBPurger;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\PHPCRPurger;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager as MongoDBDocumentManager;
use Doctrine\ODM\PHPCR\DocumentManager as PHPCRDocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\Alice\DataFixtures\Loader;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;
use Hautelook\AliceBundle\Alice\ProcessorChain;
use Hautelook\AliceBundle\Doctrine\DataFixtures\Executor\ExecutorInterface;
use Hautelook\AliceBundle\Doctrine\DataFixtures\Executor\MongoDBExecutor;
use Hautelook\AliceBundle\Doctrine\DataFixtures\Executor\ORMExecutor;
use Hautelook\AliceBundle\Doctrine\DataFixtures\Executor\PHPCRExecutor;
use Hautelook\AliceBundle\Doctrine\Finder\FixturesFinder;
use Hautelook\AliceBundle\Faker\Provider\ProviderChain;
use Hautelook\AliceBundle\Finder\FixturesFinderInterface;
use Hautelook\AliceBundle\Resolver\BundlesResolverInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Command used to load the fixtures.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class LoadDataFixturesCommand extends Command
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var FixturesFinderInterface|FixturesFinder
     */
    private $fixturesFinder;

    /**
     * @var BundlesResolverInterface
     */
    private $bundlesResolver;

    /**
     * @param string                  $name Command name
     * @param ManagerRegistry         $doctrine
     * @param LoaderInterface         $loader
     * @param FixturesFinderInterface $fixturesFinder
     * @param BundlesResolverInterface $bundlesResolver
     */
    public function __construct(
        $name,
        ManagerRegistry $doctrine,
        LoaderInterface $loader,
        FixturesFinderInterface $fixturesFinder,
        BundlesResolverInterface $bundlesResolver
    ) {
        $this->doctrine = $doctrine;
        $this->loader = $loader;
        $this->fixturesFinder = $fixturesFinder;
        $this->bundlesResolver = $bundlesResolver;

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Load data fixtures to your database.')
            ->addOption(
                'bundle',
                'b',
                InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY,
                'Bundles where fixtures should be loaded.'
            )
            ->addOption(
                'manager',
                'em',
                InputOption::VALUE_REQUIRED,
                'The entity manager to use for this command.'
            )
            ->addOption(
                'append',
                null,
                InputOption::VALUE_NONE,
                'Append the data fixtures instead of deleting all data from the database first.'
            )
        ;

        if ($this->doctrine instanceof Registry) {
            $this->addOption('purge-with-truncate',
                null,
                InputOption::VALUE_NONE,
                'Purge data by using a database-level TRUNCATE statement when using Doctrine ORM.'
            );
        }

        //TODO: set help
    }

    /**
     * {@inheritdoc}
     *
     * \RuntimeException Unsupported Application type
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (false !== strpos($input->getFirstArgument(), 'hautelook_alice:fixtures:load')
            || false !== strpos($input->getFirstArgument(), 'h:f:l')
        ) {
            $output->writeln('<comment>The use of "hautelook_alice:fixtures:load" command is deprecated and will be removed 1.1.0. Use the
"hautelook_alice:doctrine:fixtures:load" instead.</comment>');
        }

        // Warn the user that the database will be purged
        // Ask him to confirm his choice
        if ($input->isInteractive() && !$input->getOption('append')) {
            if (false === $this->askConfirmation(
                    $input,
                    $output,
                    '<question>Careful, database will be purged. Do you want to continue y/N ?</question>',
                    false
                )
            ) {
                return;
            }
        }

        $manager = $this->doctrine->getManager($input->getOption('manager'));
        $environment = $input->getOption('env');
        $bundles = $input->getOption('bundle');

        /** @var Application $application */
        $application = $this->getApplication();
        if (false === $application instanceof Application) {
            throw new \RuntimeException('Expected Symfony\Bundle\FrameworkBundle\Console\Application application.');
        }

        // Get bundles
        if (true === empty($bundles)) {
            $bundles = $application->getKernel()->getBundles();
        } else {
            $bundles = $this->bundlesResolver->resolveBundles($application, $bundles);
        }

        $fixtures = $this->fixturesFinder->getFixtures($application->getKernel(), $bundles, $environment);

        $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', 'fixtures found:'));
        foreach ($fixtures as $fixture) {
            $output->writeln(sprintf('      <comment>-</comment> <info>%s</info>', $fixture));
        }

        $newLoader = $this->getLoader($bundles, $environment);

        // Get executor
        $purgeMode = ($input->hasOption('purge-with-truncate'))? $input->getOption('purge-with-truncate'): null;
        $executor = $this->getExecutor($manager, $newLoader, $purgeMode);
        $executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });

        // Purge database and load fixtures
        $executor->execute($fixtures, $input->getOption('append'));
        $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', 'fixtures loaded'));
    }

    /**
     * Gets a fresh instance of {@see Hautelook\AliceBundle\Alice\DataFixtures\Loader} with all the options registered
     * to the hautelook_alice.fixtures.loader service and all data loaders found as faker providers.
     *
     * @param BundleInterface[] $bundles
     * @param string            $environment
     *
     * @return Loader
     */
    private function getLoader(array $bundles, $environment)
    {
        if (false === $this->fixturesFinder instanceof FixturesFinder) {
            return $this->loader;
        }

        $loaders = $this->fixturesFinder->getDataLoaders($bundles, $environment);

        return new Loader(
            new ProcessorChain($this->loader->getProcessors()),
            new ProviderChain(array_merge($this->loader->getOptions()['providers'], $loaders)),
            $this->loader->getOptions()['locale'],
            $this->loader->getOptions()['seed'],
            $this->loader->getOptions()['persist_once'],
            (true === isset($this->loader->getOptions()['logger']))? $this->loader->getOptions()['logger']: null
        );
    }

    /**
     * Gets the executor for the matching the given object manager.
     *
     * @param ObjectManager   $manager
     * @param LoaderInterface $loader
     * @param bool|null       $purgeMode
     *
     * @return ExecutorInterface
     */
    private function getExecutor(ObjectManager $manager, LoaderInterface $loader, $purgeMode)
    {
        switch (true) {
            case $manager instanceof EntityManagerInterface:
                $executor = new ORMExecutor($manager, $loader);
                $purger = new ORMPurger($manager);
                $purger->setPurgeMode(
                    $purgeMode
                        ? ORMPurger::PURGE_MODE_TRUNCATE
                        : ORMPurger::PURGE_MODE_DELETE
                );
                break;

            case $manager instanceof MongoDBDocumentManager:
                $executor = new MongoDBExecutor($manager, $loader);
                $purger = new MongoDBPurger($manager);
                break;

            case $manager instanceof PHPCRDocumentManager:
                $executor = new PHPCRExecutor($manager, $loader);
                $purger = new PHPCRPurger($manager);
                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unsupported manager type %s', get_class($manager)));
        }

        $executor->setPurger($purger);

        return $executor;
    }

    /**
     * Prompt to the user a message to ask him a confirmation.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $question
     * @param bool            $default
     *
     * @return bool User choice.
     */
    private function askConfirmation(InputInterface $input, OutputInterface $output, $question, $default)
    {
        if (false === class_exists('Symfony\Component\Console\Question\ConfirmationQuestion')) {
            /** @var DialogHelper $dialogHelper */
            $dialogHelper = $this->getHelperSet()->get('dialog');
            return $dialogHelper->askConfirmation($output, $question, $default);
        }

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion($question, $default);

        return (boolean)$questionHelper->ask($input, $output, $question);
    }
}
