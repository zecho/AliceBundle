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

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\ManagerRegistry;
use Hautelook\AliceBundle\Alice\DataFixtures\LoaderInterface;
use Hautelook\AliceBundle\Doctrine\DataFixtures\Executor\ORMExecutor;
use Hautelook\AliceBundle\Doctrine\Finder\Finder;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
     * @var Finder
     */
    private $finder;

    /**
     * @param ManagerRegistry $doctrine
     * @param LoaderInterface $loader
     * @param Finder          $finder
     */
    public function __construct(ManagerRegistry $doctrine, LoaderInterface $loader, Finder $finder)
    {
        parent::__construct();

        $this->doctrine = $doctrine;
        $this->loader = $loader;
        $this->finder = $finder;
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
            $bundles = $this->finder->resolveBundles($application, $bundles);
        }

        // Get fixtures
        $fixtures = $this->finder->getFixtures($application->getKernel(), $bundles, $input->getOption('env'));
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
