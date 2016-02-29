<?php

namespace GovWiki\DbBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class LegislationRepository
 * @package GovWiki\DbBundle\Entity\Repository
 */
class LegislationRepository extends EntityRepository
{
    /**
     * @param string $environment Environment name.
     *
     * @return \Doctrine\ORM\Query
     */
    public function getListQuery($environment)
    {
        $qb = $this->createQueryBuilder('Legislation');
        $expr = $qb->expr();

        return $qb
            ->addSelect('IssueCategory')
            ->leftJoin('Legislation.government', 'Government')
            ->leftJoin('Legislation.issueCategory', 'IssueCategory')
            ->leftJoin('Government.environment', 'Environment')
            ->where($expr->eq('Environment.slug', $expr->literal($environment)))
            ->getQuery();
    }
}
