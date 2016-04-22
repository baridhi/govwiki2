<?php

namespace GovWiki\EnvironmentBundle\Manager\Government;

use GovWiki\DbBundle\Entity\Environment;

/**
 * Interface GovernmentManagerInterface
 * @package GovWiki\EnvironmentBundle\Data\Manager\Data\Government
 */
interface GovernmentManagerInterface
{

    /**
     * Return available years for current environment.
     *
     * @param Environment $environment A Environment entity instance.
     *
     * @return integer[]
     */
    public function getAvailableYears(Environment $environment);

    /**
     * @param Environment $environment       A Environment entity instance.
     * @param array       $columnDefinitions Column definitions
     *                                       {@see EnvironmentManagerInterface::format2columnDefinition}.
     *
     * @return GovernmentManagerInterface
     *
     * @throws \Doctrine\DBAL\DBALException SQL error while creating.
     */
    public function createTable(Environment $environment, array $columnDefinitions = []);

    /**
     * @param Environment $environment A Environment entity instance.
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\DBALException SQL error while removing.
     */
    public function removeTable(Environment $environment);

    /**
     * @param Environment $environment    A Environment entity id.
     * @param string      $altTypeSlug    Slugged alt type.
     * @param string      $governmentSlug Slugged government name.
     * @param array       $parameters     Array of parameters:
     *                                    <ul>
     *                                        <li>field_name (required)</li>
     *                                        <li>limit (required)</li>
     *                                        <li>page</li>
     *                                        <li>order</li>
     *                                        <li>name_order</li>
     *                                        <li>year</li>
     *                                    </ul>.
     *
     * @return array
     */
    public function getGovernmentRank(
        Environment $environment,
        $altTypeSlug,
        $governmentSlug,
        array $parameters
    );

    /**
     * @param Environment $environment A Environment entity instance.
     * @param string      $partOfName  Part of government name.
     *
     * @return array
     */
    public function searchGovernment(Environment $environment, $partOfName);

    /**
     * @param Environment $environment A Environment entity instance.
     * @param string      $partOfName  Part of government name.
     *
     * @return array
     */
    public function searchGovernmentForComparison(
        Environment $environment,
        $partOfName
    );

    /**
     * Get revenues and expenditures by government.
     *
     * @param Environment $environment A Environment entity instance.
     * @param array       $governments Array of object, each contains id and
     *                                 year.
     *
     * @return array
     */
    public function getCategoriesForComparisonByGovernment(
        Environment $environment,
        array $governments
    );

    /**
     * Add to each governments 'data' field with specified findata caption
     * dollar amount and total for fund category.
     *
     * @param Environment $environment A Environment entity instance.
     * @param array       $data        Request in form described in
     *                                 {@see ComparisonController::compareAction}.
     *
     * @return array
     *
     * @throws \Doctrine\ORM\Query\QueryException Query result is not unique.
     */
    public function getComparedGovernments(Environment $environment, array $data);

    /**
     * @param Environment $environment A Environment entity instance.
     * @param string      $altTypeSlug Slugged government alt type.
     * @param string      $slug        Slugged government name.
     * @param integer     $year        For fetching fin data.
     *
     * @return array
     */
    public function getGovernment(
        Environment $environment,
        $altTypeSlug,
        $slug,
        $year = null
    );

    /**
     * @param Environment $environment A Environment entity instance
     * @param integer     $government  A Government entity id.
     * @param integer     $year        Data year.
     * @param array       $fields      List of fetched field names. If null -
     *                                 fetch all.
     */
    public function getEnvironmentRelatedData(
        Environment $environment,
        $government,
        $year,
        array $fields = null
    );

    /**
     * @param Environment $environment A Environment entity instance.
     * @param array       $data        New data.
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\DBALException Can't execute query.
     */
    public function updateGovernment(Environment $environment, array $data);
}