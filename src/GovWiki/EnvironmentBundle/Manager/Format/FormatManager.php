<?php

namespace GovWiki\EnvironmentBundle\Manager\Format;

use Doctrine\ORM\EntityManagerInterface;
use GovWiki\DbBundle\Entity\Environment;

/**
 * Class FormatManager
 * @package GovWiki\EnvironmentBundle\Format
 */
class FormatManager implements FormatManagerInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em A EntityManagerInterface instance.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Environment $environment, $plain = false)
    {
        return $this->em->getRepository('GovWikiDbBundle:Format')
            ->get($environment->getId(), $plain);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldFormat(Environment $environment, $fieldName)
    {
        return $this->em->getRepository('GovWikiDbBundle:Format')
            ->getOne($environment->getId(), $fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function getRankedFields(Environment $environment)
    {
        return $this->em->getRepository('GovWikiDbBundle:Format')
            ->getRankedFields($environment->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getList(Environment $environment, $altType = null)
    {
        $result = $this->em->getRepository('GovWikiDbBundle:Format')
            ->getList($environment->getId(), $altType);

        $list = [];
        foreach ($result as $item) {
            $list[$item['field']] = $item;
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function getGovernmentFields(Environment $environment)
    {
        $expr = $this->em->getExpressionBuilder();

        $tmp = $this->em->getRepository('GovWikiDbBundle:Format')
            ->createQueryBuilder('Format')
            ->select('Format.name, Format.field')
            ->where($expr->eq('Format.environment', ':environment'))
            ->setParameter('environment', $environment->getId())
            ->getQuery()
            ->getArrayResult();

        $result = [];
        foreach ($tmp as $row) {
            $result[$row['field']] = $row['name'];
        }

        return $result;
    }
}
