<?php

namespace GovWiki\DbBundle\Command;

use Doctrine\ORM\EntityManager;
use GovWiki\DbBundle\Entity\Government;
use GovWiki\DbBundle\Entity\Issue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class UpdateAuditURLsCommand extends ContainerAwareCommand
{
    public static $environments = ['puerto_rico', 'california'];
    const FILE_LIBRARY_URL = 'https://cafrs.municipalfinance.org';

    protected function configure()
    {
        $this
            ->setName('db:update-audit-urls')
            ->setDescription('Update audit URLs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new LockHandler('db:update-audit-urls');
        if (!$lock->lock()) {
            $output->writeln('This command is already running in another process.');
            return false;
        }

        /** @var EntityManager $manager */
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');

        foreach (self::$environments as $environment) {
            $sql = "SELECT * FROM {$environment}";
            $stmt = $manager->getConnection()->prepare($sql);
            $stmt->execute();
            $manager->getConnection()->beginTransaction();
            try {
                $manager->getConnection()->executeQuery("DELETE FROM issues WHERE name = 'CAFRS audits'");
                while ($row = $stmt->fetch()) {
                    $government = $manager->getRepository(Government::class)->find($row['government_id']);
                    $type = str_replace(' ', '-', strtolower($government->getType()));
                    $link = self::FILE_LIBRARY_URL . "/{$type}-{$row['year']}?s={$government->getState()}";
                    $issue = new Issue();
                    $issue
                        ->setDate((new \DateTime())->setDate($row['year'], 1, 1))
                        ->setType('audit')
                        ->setGovernment($government)
                        ->setLink($link)
                        ->setName('CAFRS audits');
                    $manager->persist($issue);
                }
                $manager->flush();
                $manager->getConnection()->commit();
            } catch (\Exception $e) {
                $manager->getConnection()->rollBack();
                $output->writeln("ERROR: {$e->getMessage()}");
            }
        }

        return true;
    }
}