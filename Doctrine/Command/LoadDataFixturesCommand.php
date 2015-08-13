<?php

namespace Hautelook\AliceBundle\Doctrine\Command;

use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\ManagerRegistry;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;
use Hautelook\AliceBundle\Doctrine\DataFixtures\Executor\ORMExecutor;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
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
     * @param ManagerRegistry $doctrine
     * @param LoaderInterface $loader
     */
    public function __construct(ManagerRegistry $doctrine, LoaderInterface $loader)
    {
        parent::__construct();

        $this->doctrine = $doctrine;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('hautelook_alice:fixtures:load')
            ->setDescription('Load data fixtures to your database.')
            ->addOption(
                'bundle',
                'b',
                InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY,
                'Bundles where fixtures should be loaded'
            )
            ->addOption(
                'environment',
                'env',
                InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY,
                'Load fixtures which belongs to the environment'
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
            ->addOption('purge-with-truncate',
                null,
                InputOption::VALUE_NONE,
                'Purge data by using a database-level TRUNCATE statement'
            )
            ;
        //TODO: set help
    }

    /**
     * {@inheritdoc}
     *
     * \RuntimeException Unsupported Application type
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Application $application */
        $application = $this->getApplication();
        if (false === $application instanceof Application) {
            throw new \RuntimeException('Expected Symfony\Bundle\FrameworkBundle\Console\Application application.');
        }

        $manager = $this->doctrine->getManager($input->getOption('manager'));

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

        // Get bundles
        $bundles = $input->getOption('bundle');
        if (true === empty($bundles)) {
            $bundles = $application->getKernel()->getBundles();
        } else {
            $bundles = $this->resolveBundles($application, $bundles);
        }

        // Get fixtures
        $fixtures = $this->getFixtures($application->getKernel(), $bundles);
        $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', 'fixtures found:'));
        foreach ($fixtures as $fixture) {
            $output->writeln(sprintf('      <comment>-</comment> <info>%s</info>', $fixture));
        }

        // Get executor
        $purger = new ORMPurger($manager);
        $purger->setPurgeMode(
            $input->getOption('purge-with-truncate')
                ? ORMPurger::PURGE_MODE_TRUNCATE
                : ORMPurger::PURGE_MODE_DELETE
        );
        $executor = new ORMExecutor($manager, $this->loader, $purger);
        $executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });

        // Purge database and load fixtures
        $executor->execute($fixtures, $input->getOption('append'));
        $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', 'fixtures loaded'));
    }

    /**
     * Look at all the bundles registered in the application to return them. An exception is thrown if a bundle has
     * not been found.
     *
     * @param Application $application Application in which bundles will be looked in
     * @param string[]    $names       Bundle names
     *
     * @return \Symfony\Component\HttpKernel\Bundle\BundleInterface[]
     *
     * @throws \RuntimeException A bundle could not be resolved.
     */
    private function resolveBundles(Application $application, array $names)
    {
        $bundles = $application->getKernel()->getBundles();

        $result  = [];
        foreach ($names as $name) {
            if (false === isset($bundles[$name])) {
                throw new \RuntimeException(sprintf(
                    'The bundle "%s" was not found. Bundles availables are: %s.',
                    $name,
                    implode('", "', array_keys($bundles))
                ));
            }

            $result[$name] = $bundles[$name];
        }

        return $result;
    }

    /**
     * Get all fixtures (paths).
     *
     * @param KernelInterface   $kernel
     * @param BundleInterface[] $bundles
     *
     * @return \string[]
     */
    private function getFixtures(KernelInterface $kernel, array $bundles)
    {
        $loadersPaths = $this->getLoadersPaths($bundles);
        $loader = new Loader();

        // Add all fixtures to the new Doctrine loader
        foreach ($loadersPaths as $path) {
            if (true === is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
        }

        // Get all registered fixtures
        $fixtures = [];
        foreach ($loader->getFixtures() as $loader) {
            /** @var Loader $loader */
            $fixtures = array_merge($fixtures, $loader->getFixtures());
        }

        if (0 === count($fixtures)) {
            throw new \InvalidArgumentException(
                sprintf('Could not find any fixtures to load in: %s', "\n\n- ".implode("\n- ", $loadersPaths))
            );
        }

        // Get real fixtures path
        foreach ($fixtures as $index => $fixture) {
            if ('@' === $fixture[0]) {
                $fixtures[$index] = $kernel->locateResource($fixture);
            } else {
                $realPath = realpath($fixture);
                if (false === $realPath) {
                    throw new \InvalidArgumentException(sprintf('The file "%s" was not found', $fixture));
                }
                $fixtures[$index] = realpath($fixture);
            }
        }

        return array_unique($fixtures);
    }

    /**
     * Get paths to loaders.
     *
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface[] $bundles
     *
     * @return array
     */
    private function getLoadersPaths(array $bundles)
    {
        $paths = [];
        foreach ($bundles as $bundle) {
            $paths[] = $bundle->getPath().'/DataFixtures/ORM';
        }

        return $paths;
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

        return $questionHelper->ask($input, $output, $question);
    }
}
