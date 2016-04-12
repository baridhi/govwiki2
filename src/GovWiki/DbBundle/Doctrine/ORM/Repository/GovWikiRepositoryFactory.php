<?php

namespace GovWiki\DbBundle\Doctrine\ORM\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;

/**
 * Class GovWikiRepositoryFactory
 * @package GovWiki\DbBundle\Doctrine\ORM\Repository
 */
class GovWikiRepositoryFactory extends DefaultRepositoryFactory
{
    public function getRepository(EntityManagerInterface $entityManager, $entityName)
    {
        dump('in new factory');
        return parent::getRepository($entityManager, $entityName); // TODO: Change the autogenerated stub
    }


}
