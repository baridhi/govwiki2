<?php

namespace GovWiki\DbBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use GovWiki\DbBundle\Entity\ElectedOfficial;
use GovWiki\DbBundle\Entity\Government;
use GovWiki\DbBundle\Utils\Functions;

/**
 * GovernmentRepository
 */
class GovernmentRepository extends EntityRepository
{
    /**
     * @param string  $environment Environment name.
     * @param integer $id          Government id.
     * @param string  $name        Government name.
     *
     * @return Query
     */
    public function getListQuery($environment, $id = null, $name = null)
    {
        $qb = $this
            ->createQueryBuilder('Government')
            ->leftJoin('Government.environment', 'Environment');

        $expr = $qb->expr();

        $qb->where($expr->eq('Environment.slug', $expr->literal($environment)));

        if (null !== $id) {
            $qb->andWhere($expr->eq('Government.id', $id));
        }
        if (null !== $name) {
            $qb->andWhere($expr->like(
                'Government.name',
                $expr->literal('%'.$name.'%')
            ));
        }

        return $qb->getQuery();
    }
//    /**
//     * @param string $altTypeSlug Government slugged alt type.
//     *
//     * @return integer
//     */
//    public function countGovernments($altTypeSlug)
//    {
//        $qb = $this->createQueryBuilder('Government');
//        return $qb
//            ->select($qb->expr()->count('Government.id'))
//            ->where(
//                $qb->expr()->eq(
//                    'Government.altTypeSlug',
//                    $qb->expr()->literal($altTypeSlug)
//                )
//            )
//            ->getQuery()
//            ->getSingleScalarResult();
//    }

//    /**
//     * @param string  $altTypeSlug Government slugged alt type.
//     * @param integer $page        Page to show.
//     * @param integer $limit       Max entities per page.
//     * @param array   $orderFields Assoc array, fields name as key and
//     *                             sort direction ('desc' or 'asc' (default))
//     *                             as value.
//     *
//     * @return string[]
//     */
//    public function getGovernments($altTypeSlug, $page, $limit, array $orderFields = [])
//    {
//        $qb = $this->createQueryBuilder('Government');
//        $qb
//            ->select('Government.name, Government.altTypeSlug, Government.slug')
//            ->where(
//                $qb->expr()->eq(
//                    'Government.altTypeSlug',
//                    $qb->expr()->literal($altTypeSlug)
//                )
//            )
//            ->setFirstResult($page * $limit)
//            ->setMaxResults($limit);
//
//        /*
//        * Get all class property with 'Rank' postfix.
//        */
//        foreach ($this->getClassMetadata()->columnNames as $key => $value) {
//            if (false !== strpos($key, 'Rank')) {
//                $qb->addSelect("Government.$key");
//            }
//        }
//
//        if ($orderFields) {
//            foreach ($orderFields as $fieldName => $direction) {
//                $fieldName .= 'Rank';
//                if ('desc' === $direction) {
//                    $qb->addOrderBy($qb->expr()->desc('Government.'. $fieldName));
//                } else {
//                    $qb->addOrderBy($qb->expr()->asc('Government.'. $fieldName));
//                }
//            }
//        }
//
//        $data = $qb
//            ->getQuery()
//            ->getArrayResult();
//
//        /*
//         * Remove empty fields.
//         */
//        foreach ($data as &$row) {
//            foreach ($row as $key => $field) {
//                if (empty($field)) {
//                    unset($row[$key]);
//                }
//            }
//        }
//
//        return $data;
//    }

    /**
     * @param string  $environment    Environment name.
     * @param string  $altTypeSlug    Slugged alt type.
     * @param string  $governmentSlug Slugged government name.
     * @param string  $rankFieldName  One of government rank field name.
     * @param integer $limit          Max result per page.
     * @param integer $page           Page index, start from 0.
     * @param string  $order          Sorting order by $rankFieldName 'desc' or
     *                                'asc'. If null start from given entity.
     * @param string  $nameOrder      Sorting order by government name 'desc'
     *                                or 'asc'. If null start from given entity.
     *
     * @return array
     */
    public function getGovernmentRank(
        $environment,
        $altTypeSlug,
        $governmentSlug,
        $rankFieldName,
        $limit,
        $page = 0,
        $order = null,
        $nameOrder = null
    ) {
        $fieldName = preg_replace('|_rank$|', '', $rankFieldName);

        error_log('Field name without rank: '. $fieldName);

        $con = $this->_em->getConnection();

        $qb = $con->createQueryBuilder();
        $expr = $qb->expr();

        $qb
            ->select(
                "government.slug AS name, extra.{$fieldName} AS amount",
                "extra.{$rankFieldName} AS value"
            )
            ->from($environment, 'extra')
            ->innerJoin(
                'extra',
                'governments',
                'government',
                'extra.government_id = government.id'
            )
            ->where($expr->eq(
                'government.alt_type_slug',
                $expr->literal($altTypeSlug)
            ));

        /*
         * Get list of rank started from given government.
         */
        if (empty($order)) {
            /*
             * Get rank for given government.
             */
            $qb2 = clone $qb;
            $qb2
                ->select("extra.{$rankFieldName}")
                ->andWhere(
                    $expr
                        ->eq('government.slug', $expr->literal($governmentSlug))
                )
                ->orderBy("extra.{$rankFieldName}", 'asc')
                ->setMaxResults(1);

            $qb->andWhere(
                $expr->gte("extra.{$rankFieldName}", '('. $qb2->getSQL() .')')
            );

            if (empty($nameOrder)) {
                $qb->orderBy("extra.{$rankFieldName}", 'asc');
            }
        /*
         * Get sorted information from offset computed on given page and limit.
         */
        } elseif ('desc' === $order) {
            $qb->addOrderBy("extra.{$fieldName}", 'desc');
        } elseif ('asc' === $order) {
            $qb->addOrderBy("extra.{$fieldName}", 'asc');
        }

        if ('desc' === $nameOrder) {
            $qb->orderBy('government.slug', 'desc');
        } elseif ('asc' === $nameOrder) {
            $qb->orderBy('government.slug', 'asc');
        }

        $qb
            ->setFirstResult($page * $limit)
            ->setMaxResults($limit);

        error_log('Exec query: '. $qb->getSQL());

        return $con->fetchAll($qb->getSQL());
    }

    /**
     * Find government by slug and altTypeSlug.
     * Append maxRanks and financialStatements.
     *
     * @param string $environment Environment name.
     * @param string $altTypeSlug Slugged government alt type.
     * @param string $slug        Slugged government name.
     *
     * @return array|null
     */
    public function findGovernment($environment, $altTypeSlug, $slug)
    {
        $qb = $this->createQueryBuilder('Government');
        $expr = $qb->expr();

        $data = $qb
            ->select(
                'Government, FinData, CaptionCategory, ElectedOfficial, Fund'
            )
            ->leftJoin('Government.finData', 'FinData')
            ->leftJoin('FinData.captionCategory', 'CaptionCategory')
            ->leftJoin('Government.electedOfficials', 'ElectedOfficial')
            ->leftJoin('FinData.fund', 'Fund')
            ->leftJoin('Government.environment', 'Environment')
            ->where(
                $expr->andX(
                    $expr->eq(
                        'Government.altTypeSlug',
                        $qb->expr()->literal($altTypeSlug)
                    ),
                    $expr->eq(
                        'Government.slug',
                        $qb->expr()->literal($slug)
                    ),
                    $expr->eq('Environment.slug', $expr->literal($environment))
                )
            )
            ->orderBy($expr->asc('CaptionCategory.id'))
            ->getQuery()
            ->getArrayResult();

        if (count($data) <= 0) {
            return null;
        }

        $government = $data[0];

        $financialStatementsGroups = [];
        $finData = $government['finData'];
        foreach ($finData as $finDataItem) {
            $financialStatementsGroups[$finDataItem['caption']][] = $finDataItem;
        }
        $i = 0;
        $financialStatements = [];
        foreach ($financialStatementsGroups as $caption => $finData) {
            foreach ($finData as $finDataItem) {
                $financialStatements[$i]['caption'] = $caption;
                $financialStatements[$i]['category_name'] = $finDataItem['captionCategory']['name'];
                $financialStatements[$i]['display_order'] = $finDataItem['displayOrder'];
                if (empty($financialStatements[$i]['genfund'])) {
                    if (empty($finDataItem['fund'])) {
                        $financialStatements[$i]['genfund'] = $finDataItem['dollarAmount'];
                    } elseif ($finDataItem['fund']['name'] === 'General Fund') {
                        $financialStatements[$i]['genfund'] = $finDataItem['dollarAmount'];
                    }
                }
                if (empty($financialStatements[$i]['otherfunds'])) {
                    if (!empty($finDataItem['fund']) and $finDataItem['fund']['name'] === 'Other') {
                        $financialStatements[$i]['otherfunds'] = $finDataItem['dollarAmount'];
                    }
                }
                if (empty($financialStatements[$i]['totalfunds'])) {
                    if (!empty($finDataItem['fund']) and $finDataItem['fund']['name'] === 'Total') {
                        $financialStatements[$i]['totalfunds'] = $finDataItem['dollarAmount'];
                    }
                }
            }
            $i++;
        }

        unset($government['finData']);

        $financialStatements = Functions::groupBy(
            $financialStatements,
            [ 'category_name', 'caption' ]
        );

        $government['financialStatements'] = $financialStatements;

        return $government;
    }

    /**
     * Search government with name like given in partOfName parameter.
     *
     * @param string $environment Environment name.
     * @param string $partOfName  Part of government name.
     *
     * @return array
     */
    public function search($environment, $partOfName)
    {
        $qb = $this->createQueryBuilder('Government');
        $expr = $qb->expr();

        return $qb
            ->select(
                'partial Government.{id, name, type, state, slug, altTypeSlug}'
            )
            ->leftJoin('Government.environment', 'Environment')
            ->where(
                $expr->andX(
                    $expr->eq('Environment.slug', $expr->literal($environment)),
                    $expr->like(
                        'Government.name',
                        $expr->literal('%'.$partOfName.'%')
                    )
                )
            )
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Get markers for map.
     *
     * @param  array   $altTypes Ignored altTypes.
     * @param  integer $limit    Max Markers.
     * @return array
     */
    public function getMarkers($altTypes, $limit = 200)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g.id', 'g.name', 'g.altType', 'g.type', 'g.city', 'g.zip', 'g.state', 'g.latitude', 'g.longitude', 'g.altTypeSlug', 'g.slug');
//            ->where('g.altType != :altType')
//            ->setParameter('altType', $altTypes);

        if (!empty($altTypes)) {
            $orX = $qb->expr()->orX();
            $parameters = [];
            foreach ($altTypes as $key => $type) {
                if ($type != 'Special District') {
                    $orX->add($qb->expr()->eq('g.altType', ':altType'.$key));
                    $parameters['altType'.$key]  = $type;
                }
            }
//            $parameters['altType'] = 'County';
            $qb->andWhere($orX)->setParameters($parameters);
        }

        $result = $qb->setMaxResults($limit)->orderBy('g.rand', 'ASC')->getQuery()->getArrayResult();

        if (!empty($altTypes) && in_array('Special District', $altTypes)) {
            $specialDistricts = $this->fetchSpecialDistricts();
            $result = array_merge($result, $specialDistricts);
        }

        return $result;
    }

    /**
     * Get list of all elected officials from government by one of
     * elected official.
     *
     * @param integer $id Elected official id.
     *
     * @return ElectedOfficial[]
     */
    public function governmentElectedOfficial($id)
    {
        $qb2 = $this->createQueryBuilder('Gov2');
        $qb2
            ->select('Gov2.id')
            ->join('Gov2.electedOfficials', 'EO2')
            ->where(
                $qb2->expr()->eq('EO2.id', $id)
            );

        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb
            ->from('GovWikiDbBundle:ElectedOfficial', 'EO')
            ->select('EO.id, EO.fullName')
            ->where(
                $qb->expr()->in('EO.government', $qb2->getDQL())
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * Compute max ranks for given alt type.
     *
     * @param string $altType One of the government alt type.
     *
     * @return array
     */
    public function computeMaxRanks($altType)
    {
        $qb = $this->createQueryBuilder('Government');

        /*
         * Get all class property with 'Rank' postfix.
         */
        $fields = [];
        foreach ($this->getClassMetadata()->columnNames as $key => $value) {
            $pos = strpos($key, 'Rank');
            if (false !== $pos) {
                $fields[] = $qb->expr()->max("Government.$key") .
                    ' AS ' . substr($key, 0, $pos) . 'MaxRank';
            }
        }

        $qb
            ->select($fields)
            ->where(
                $qb->expr()->eq(
                    'Government.altType',
                    $qb->expr()->literal($altType)
                )
            );

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $map Map name.
     *
     * @return array
     */
    public function exportGovernments($map)
    {
        $qb = $this->createQueryBuilder('Government');
        $expr = $qb->expr();

        return $qb
            ->select(
                'partial Government.{id,latitude,longitude,slug,altTypeSlug}'
            )
            ->leftJoin('Government.map', 'Map')
            ->where($expr->eq('Map.name', $expr->literal($map)))
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array
     */
    private function fetchSpecialDistricts()
    {
        $specialDistrictsList  = [
            4378, 4387, 4416, 4427, 4532, 4750, 4917,
            4981, 5204, 5339, 5600, 5618, 5626, 5749,
            5752, 5791, 5871, 5874, 5963, 5983, 5993,
            5995, 6000, 6033, 6070, 6090, 6093, 6544
        ];

        $qb = $this->createQueryBuilder('g')
            ->select('g.id', 'g.name', 'g.altType', 'g.type', 'g.city', 'g.zip', 'g.state', 'g.latitude', 'g.longitude', 'g.altTypeSlug', 'g.slug');

        $orX = $qb->expr()->orX();
        $parameters = [];
        foreach ($specialDistrictsList as $key => $id) {
            $orX->add($qb->expr()->eq('g.id', ':id'.$key));
            $parameters['id'.$key] = $id;
        }

        $qb->andWhere($orX)->setParameters($parameters);

        return $qb->getQuery()->getArrayResult();
    }
}
