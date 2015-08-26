<?php

namespace GovWiki\DbBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Endorsement
 *
 * @ORM\Table(name="endorsements")
 * @ORM\Entity
 */
class Endorsement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name_of_endorser", type="string", length=255)
     */
    private $nameOfEndorser;

    /**
     * @var string
     *
     * @ORM\Column(name="endorser_type", type="string", length=255)
     */
    private $endorserType;

    /**
     * @var integer
     *
     * @ORM\Column(name="election_year", type="integer")
     */
    private $electionYear;

    /**
     * @ORM\ManyToOne(targetEntity="ElectedOfficial", inversedBy="endorsments")
     */
    private $electedOfficial;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nameOfEndorser
     *
     * @param string $nameOfEndorser
     * @return Endorsement
     */
    public function setNameOfEndorser($nameOfEndorser)
    {
        $this->nameOfEndorser = $nameOfEndorser;

        return $this;
    }

    /**
     * Get nameOfEndorser
     *
     * @return string
     */
    public function getNameOfEndorser()
    {
        return $this->nameOfEndorser;
    }

    /**
     * Set endorserType
     *
     * @param string $endorserType
     * @return Endorsement
     */
    public function setEndorserType($endorserType)
    {
        $this->endorserType = $endorserType;

        return $this;
    }

    /**
     * Get endorserType
     *
     * @return string
     */
    public function getEndorserType()
    {
        return $this->endorserType;
    }

    /**
     * Set electionYear
     *
     * @param integer $electionYear
     * @return Endorsement
     */
    public function setElectionYear($electionYear)
    {
        $this->electionYear = $electionYear;

        return $this;
    }

    /**
     * Get electionYear
     *
     * @return integer
     */
    public function getElectionYear()
    {
        return $this->electionYear;
    }

    /**
     * Set electedOfficial
     *
     * @param \GovWiki\DbBundle\Entity\ElectedOfficial $electedOfficial
     * @return Endorsement
     */
    public function setElectedOfficial(\GovWiki\DbBundle\Entity\ElectedOfficial $electedOfficial = null)
    {
        $this->electedOfficial = $electedOfficial;

        return $this;
    }

    /**
     * Get electedOfficial
     *
     * @return \GovWiki\DbBundle\Entity\ElectedOfficial
     */
    public function getElectedOfficial()
    {
        return $this->electedOfficial;
    }
}
