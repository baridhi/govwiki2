<?php

namespace GovWiki\EnvironmentBundle\Strategy;

use GovWiki\DbBundle\Entity\Environment;

/**
 * Interface GovwikiNamingStrategy
 * @package GovWiki\EnvironmentBundle\Strategy
 */
final class GovwikiNamingStrategy
{
    /**
     * @param Environment $environment A Environment entity instance.
     *
     * @return string
     */
    public static function environmentRelatedTableName(Environment $environment)
    {
        return $environment->getSlug();
    }

    /**
     * @param string $field Original field name.
     *
     * @return string
     */
    public static function rankedFieldName($field)
    {
        return $field .'_rank';
    }

    /**
     * @param string $field Field name.
     *
     * @return boolean
     */
    public static function isRankedName($field)
    {
        return strpos($field, '_rank') === (strlen($field) - 5);
    }

    /**
     * @param string $rankFieldName Ranked field name.
     *
     * @return string
     */
    public static function originalFromRankFieldName($rankFieldName)
    {
        return substr($rankFieldName, 0, strpos($rankFieldName, '_rank'));
    }

    /**
     * @param Environment $environment A Environment entity instance.
     *
     * @return string
     */
    public static function maxRanksTableName(Environment $environment)
    {
        return $environment->getSlug() .'_max_ranks';
    }

    /**
     * @param Environment $environment A Environment entity instance.
     *
     * @return string
     */
    public static function cartoDbDatasetName(Environment $environment)
    {
        return $environment->getCartoDBPrefix()
            . self::environmentRelatedTableName($environment);
    }

    /**
     * @param Environment $environment A Environment entity instance.
     *
     * @return string
     */
    public static function cartoDbBackupDatasetName(Environment $environment)
    {
        return self::cartoDbDatasetName($environment) . '_backup';
    }
}
