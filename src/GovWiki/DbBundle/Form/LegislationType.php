<?php

namespace GovWiki\DbBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LegislationType
 * @package GovWiki\DbBundle\Form
 */
class LegislationType extends AbstractType
{
    /**
     * @var string
     */
    private $environment;

    /**
     * @param string $environment Environment name.
     */
    public function __construct($environment = null)
    {
        $this->environment = $environment;
    }

    /**
     * @param string $environment Environment name.
     *
     * @return LegislationType
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $environment = $this->environment;

        $builder
            ->add('govAssignedNumber')
            ->add('dateConsidered', 'date')
            ->add('name')
            ->add('summary')
            ->add('evaluatorApprovedPosition')
            ->add('weighting')
            ->add('notes')
            ->add('issueCategory', 'entity', [
                'class' => 'GovWiki\DbBundle\Entity\IssueCategory',
                'choice_label' => 'name',
            ])
            ->add('electedOfficialVotes', 'collection', [
                'type' => new ElectedOfficialVoteType(),
                'by_reference' => 'false',
            ])
            ->add('government', 'entity', [
                'class' => 'GovWiki\DbBundle\Entity\Government',
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'government',
                ],
                'query_builder' => function (EntityRepository $repository)
                    use ($environment) {
                    /*
                     * Select governments only from given environment.
                     */
                    $qb = $repository->createQueryBuilder('Government');
                    $expr = $qb->expr();

                    return $qb
                        ->select('partial Government.{id,name}')
                        ->leftJoin('Government.environment', 'Environment')
                        ->where($expr->eq(
                            'Environment.slug',
                            $expr->literal($environment)
                        ));
                },
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'GovWiki\DbBundle\Entity\Legislation',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'govwiki_dbbundle_legislation';
    }
}
