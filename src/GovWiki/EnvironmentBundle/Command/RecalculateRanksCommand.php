<?php

namespace GovWiki\EnvironmentBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use GovWiki\DbBundle\Entity\Environment;
use GovWiki\EnvironmentBundle\GovWikiEnvironmentService;
use GovWiki\EnvironmentBundle\Manager\Format\FormatManagerInterface;
use GovWiki\EnvironmentBundle\Manager\Government\GovernmentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RecalculateRanksCommand
 * @package GovWiki\EnvironmentBundle\Command
 */
class RecalculateRanksCommand extends ContainerAwareCommand
{

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('govwiki:recalculate:ranks')
            ->setDescription('Recalculate ranks for given GovWiki environment.')
            ->addArgument('environment', InputArgument::REQUIRED, 'Environment domain.');
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return null|int null or 0 if everything went fine, or an error code.
     *
     * @throws \LogicException When this abstract method is not implemented.
     *
     * @see setCode()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Registry $doctrine */
        $doctrine = $this->getContainer()->get('doctrine');
        $repository = $doctrine->getRepository('GovWikiDbBundle:Environment');

        // Get environment entity by name from user input.
        $name = $input->getArgument('environment');
        $environment = $repository->findOneBy([ 'domain' => $name ]);

        if (! $environment instanceof Environment) {
            $message = "<error>Can't find environment with name: {$name}</error>";
            $output->writeln($message);

            return 1;
        }

        $this->getContainer()->get(GovWikiEnvironmentService::RANK_MANAGER)
            ->compute($environment);

        return 0;
    }
}
