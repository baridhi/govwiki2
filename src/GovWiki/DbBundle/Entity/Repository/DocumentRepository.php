<?php

namespace GovWiki\DbBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentRepository extends EntityRepository
{

    /**
     * @param integer $government A government entity id.
     * @param string  $type       Filter by document type.
     * @param integer $year       Filter by document year.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getListQuery($government, $type = null, $year = null)
    {
        $expr = $this->_em->getExpressionBuilder();
        $qb = $this->createQueryBuilder('Document')
            ->where($expr->eq('Document.government', ':government'))
            ->setParameter('government', $government);

        // Filter by type.
        if ($type) {
            $qb
                ->andWhere($expr->eq('Document.type', ':type'))
                ->setParameter('type', $type);
        }

        // Filter by year.
        if ($year) {
            $qb
                ->andWhere($expr->eq('YEAR(Document.date)', ':year'))
                ->setParameter('year', $year);
        }

        return $qb;
    }
}
