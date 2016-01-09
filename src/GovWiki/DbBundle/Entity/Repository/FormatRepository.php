<?php

namespace GovWiki\DbBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use GovWiki\DbBundle\Utils\Functions;

/**
 * FormatRepository
 */
class FormatRepository extends EntityRepository
{
    /**
     * @param string  $environment Environment name.
     * @param boolean $full        Flag.
     *
     * @return \Doctrine\ORM\Query
     */
    public function getListQuery($environment, $full = false)
    {
        $qb = $this->createQueryBuilder('Format');
        $expr = $qb->expr();

        if ($full) {
            $qb->select(
                'Format.helpText, Format.dataOrFormula, Format.description',
                'Format.mask, Format.field, Format.ranked, Format.showIn'
            );
        } else {
            $qb->select('Format.id, Format.field, Format.description');
        }

        return $qb
            ->addSelect(
                'Tab.name AS tab_name, Category.name AS category_name'
            )
            ->leftJoin('Format.environment', 'Environment')
            ->leftJoin('Format.category', 'Category')
            ->leftJoin('Format.tab', 'Tab')
            ->where($expr->eq('Environment.slug', $expr->literal($environment)))
            ->orderBy($expr->asc('Tab.orderNumber'))
            ->addOrderBy($expr->asc('Tab.name'))
            ->addOrderBy($expr->asc('Category.orderNumber'))
            ->addOrderBy($expr->asc('Category.name'))
            ->getQuery();
    }

    /**
     * @param string  $environment Environment name.
     * @param boolean $plain       Flag, if set return plain array without
     *                             grouping by tab names and fields.
     *
     * @return array
     */
    public function get($environment, $plain = false)
    {
        $result = $this->getListQuery($environment, true)
            ->getArrayResult();

        if ($plain) {
            return $result;
        }
        return Functions::groupBy($result, [ 'tab_name', 'field' ]);
    }
}
