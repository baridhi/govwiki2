<?php

namespace GovWiki\DbBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use GovWiki\UserBundle\Entity\User;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Government
 *
 * @ORM\Table(name="governments")
 * @ORM\Entity(repositoryClass="GovWiki\DbBundle\Entity\Repository\GovernmentRepository")
 * @ExclusionPolicy("none")
 *
 * @UniqueEntity("name")
 */
class Government
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"government", "government_list", "elected_official", "map"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="state_id", type="string", length=255, nullable=true)
     * @Groups({"government", "elected_official"})
     */
    private $stateId;

    /**
     * @ORM\OneToMany(
     *  targetEntity="ElectedOfficial",
     *  mappedBy="government",
     *  cascade={"remove"}
     * )
     * @MaxDepth(2)
     * @Groups({"government"})
     */
    private $electedOfficials;

    /**
     * @ORM\OneToMany(
     *  targetEntity="Legislation",
     *  mappedBy="government",
     *  cascade={"remove"}
     * )
     */
    private $legislations;

    /**
     * @ORM\OneToMany(
     *  targetEntity="FinData",
     *  mappedBy="government",
     *  cascade={"remove"}
     * )
     * @Groups({"government"})
     */
    private $finData;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Groups({"government", "government_list"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     * @Slug(fields={"name"}, separator="_", style="camel")
     * @Groups({"government", "government_list", "elected_official", "map"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     * @Groups({"government", "government_list"})
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="alt_type", type="string", length=20, nullable=true)
     * @Groups({"government", "government_list"})
     */
    private $altType;

    /**
     * @var string
     *
     * @ORM\Column(name="alt_type_slug", type="string", length=20, nullable=true)
     * @Slug(fields={"altType"}, separator="_", style="camel", unique=false)
     * @Groups({"government", "elected_official", "map"})
     */
    private $altTypeSlug;

    /**
     * @var string
     *
     * @ORM\Column(name="census_contact", type="string", length=255, nullable=true)
     * @Groups({"government"})
     */
    private $censusContact;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @Groups({"government", "elected_official"})
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=50, nullable=true)
     * @Groups({"government"})
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=50, nullable=true)
     * @Groups({"government"})
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="county_area_name", type="text", nullable=true)
     * @Groups({"government"})
     */
    private $countyAreaName;

    /**
     * @var string
     *
     * @ORM\Column(name="wikipedia_page_name", type="string", length=255, nullable=true)
     * @Groups({"government"})
     */
    private $wikipediaPageName;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $county = false;

    /**
     * @var Environment
     *
     * @ORM\ManyToOne(targetEntity="Environment", inversedBy="governments")
     */
    private $environment;

    /**
     * @var string
     *
     * @ORM\Column(name="secondary_logo_path", nullable=true)
     */
    private $secondaryLogoPath;

    /**
     * @var string
     *
     * @ORM\Column(name="secondary_logo_url", nullable=true)
     */
    private $secondaryLogoUrl;

    /**
     * @var UploadedFile
     */
    private $secondaryLogo;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $comment = false;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $image;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(
     *  targetEntity="GovWiki\UserBundle\Entity\User",
     *  inversedBy="subscribedTo"
     * )
     * @ORM\JoinTable(name="cross_governments_users")
     */
    private $subscribers;

    /**
     * @ORM\OneToOne(targetEntity="Chat", cascade={"persist"})
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id")
     */
    private $chat;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Salary", mappedBy="government")
     */
    private $salaries;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Pension", mappedBy="government")
     */
    private $pensions;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="government", cascade={
     *  "remove"
     * })
     */
    private $issues;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $webSiteAddress;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->electedOfficials = new ArrayCollection();
        $this->finData          = new ArrayCollection();
        $this->legislations     = new ArrayCollection();
        $this->subscribers      = new ArrayCollection();
        $this->salaries         = new ArrayCollection();
        $this->pensions         = new ArrayCollection();
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

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
     * Set stateId
     *
     * @param string $stateId
     *
     * @return Government
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * Get stateId
     *
     * @return string
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Government
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->slug = self::slugifyName($name);

        return $this;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function slugifyName($string)
    {
        $slug = str_replace([' ', '-'], '_', str_replace(['County Of ', 'City Of '], '', ucwords(strtolower($string))));
        $slug = preg_replace('/\W/', '', $slug);
        return $slug;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Government
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Government
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set altType
     *
     * @param string $altType
     *
     * @return Government
     */
    public function setAltType($altType)
    {
        $this->altType = $altType;
        $this->altTypeSlug = self::slugifyName($altType);

        return $this;
    }

    /**
     * @param $string
     *
     * @return string
     */
    public static function slugifyAltType($string)
    {
        return str_replace(' ', '_', ucwords(strtolower($string)));
    }

    /**
     * Get altType
     *
     * @return string
     */
    public function getAltType()
    {
        return $this->altType;
    }

    /**
     * Set altTypeSlug
     *
     * @param string $altTypeSlug
     *
     * @return Government
     */
    public function setAltTypeSlug($altTypeSlug)
    {
        $this->altTypeSlug = $altTypeSlug;

        return $this;
    }

    /**
     * Get altTypeSlug
     *
     * @return string
     */
    public function getAltTypeSlug()
    {
        return $this->altTypeSlug;
    }

    /**
     * Set censusContact
     *
     * @param string $censusContact
     *
     * @return Government
     */
    public function setCensusContact($censusContact)
    {
        $this->censusContact = $censusContact;

        return $this;
    }

    /**
     * Get censusContact
     *
     * @return string
     */
    public function getCensusContact()
    {
        return $this->censusContact;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Government
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Government
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Government
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set webSite
     *
     * @param string $webSite
     *
     * @return Government
     */
    public function setWebSite($webSite)
    {
        $this->webSite = $webSite;

        return $this;
    }

    /**
     * Get webSite
     *
     * @return string
     */
    public function getWebSite()
    {
        return $this->webSite;
    }

    /**
     * Set countyAreaName
     *
     * @param string $countyAreaName
     *
     * @return Government
     */
    public function setCountyAreaName($countyAreaName)
    {
        $this->countyAreaName = $countyAreaName;

        return $this;
    }

    /**
     * Get countyAreaName
     *
     * @return string
     */
    public function getCountyAreaName()
    {
        return $this->countyAreaName;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return Government
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return Government
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set rand
     *
     * @param float $rand
     *
     * @return Government
     */
    public function setRand($rand)
    {
        $this->rand = $rand;

        return $this;
    }

    /**
     * Get rand
     *
     * @return float
     */
    public function getRand()
    {
        return $this->rand;
    }

    /**
     * Set incId
     *
     * @param integer $incId
     *
     * @return Government
     */
    public function setIncId($incId)
    {
        $this->incId = $incId;

        return $this;
    }

    /**
     * Get incId
     *
     * @return integer
     */
    public function getIncId()
    {
        return $this->incId;
    }

    /**
     * Set wikipediaPageName
     *
     * @param string $wikipediaPageName
     *
     * @return Government
     */
    public function setWikipediaPageName($wikipediaPageName)
    {
        $this->wikipediaPageName = $wikipediaPageName;

        return $this;
    }

    /**
     * Get wikipediaPageName
     *
     * @return string
     */
    public function getWikipediaPageName()
    {
        return $this->wikipediaPageName;
    }

    /**
     * Set county
     *
     * @param boolean $county
     *
     * @return Government
     */
    public function setCounty($county)
    {
        $this->county = $county;

        return $this;
    }

    /**
     * Get county
     *
     * @return boolean
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * Add electedOfficial
     *
     * @param ElectedOfficial $electedOfficial A ElectedOfficial instance.
     *
     * @return Government
     */
    public function addElectedOfficial(ElectedOfficial $electedOfficial)
    {
        $this->electedOfficials[] = $electedOfficial;

        return $this;
    }

    /**
     * Remove electedOfficial
     *
     * @param ElectedOfficial $electedOfficial A ElectedOfficial instance.
     *
     * @return Government
     */
    public function removeElectedOfficial(ElectedOfficial $electedOfficial)
    {
        $this->electedOfficials->removeElement($electedOfficial);

        return $this;
    }

    /**
     * Get electedOfficials
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getElectedOfficials()
    {
        return $this->electedOfficials;
    }

    /**
     * Add legislations
     *
     * @param Legislation $legislation A Legislation instance.
     *
     * @return Government
     */
    public function addLegislation(Legislation $legislation)
    {
        $this->legislations[] = $legislation;

        return $this;
    }

    /**
     * Remove legislations
     *
     * @param Legislation $legislation A Legislation instance.
     *
     * @return Government
     */
    public function removeLegislation(Legislation $legislation)
    {
        $this->legislations->removeElement($legislation);

        return $this;
    }

    /**
     * Get legislations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLegislations()
    {
        return $this->legislations;
    }

    /**
     * Add finData
     *
     * @param FinData $finData A FinData instance.
     *
     * @return Government
     */
    public function addFinDatum(FinData $finData)
    {
        $this->finData[] = $finData;

        return $this;
    }

    /**
     * Remove finData
     *
     * @param FinData $finData A FinData instance.
     *
     * @return Government
     */
    public function removeFinDatum(FinData $finData)
    {
        $this->finData->removeElement($finData);

        return $this;
    }

    /**
     * Get finData
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFinData()
    {
        return $this->finData;
    }

    /**
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param Environment $environment A Environment instance.
     *
     * @return Government
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Set secondaryLogoUrl
     *
     * @param string $secondaryLogoUrl
     * @return Government
     */
    public function setSecondaryLogoUrl($secondaryLogoUrl)
    {
        if (strpos($secondaryLogoUrl, 'http://') === false) {
            $secondaryLogoUrl = 'http://' . $secondaryLogoUrl;
        }
        $this->secondaryLogoUrl = $secondaryLogoUrl;

        return $this;
    }

    /**
     * Get secondaryLogoUrl
     *
     * @return string
     */
    public function getSecondaryLogoUrl()
    {
        return $this->secondaryLogoUrl;
    }

    /**
     * @return UploadedFile
     */
    public function getSecondaryLogo()
    {
        return $this->secondaryLogo;
    }

    /**
     * @param UploadedFile $secondaryLogo A UploadedFile instance.
     */
    public function setSecondaryLogo(UploadedFile $secondaryLogo)
    {
        $this->secondaryLogo = $secondaryLogo;
    }

    /**
     * Set secondaryLogoPath
     *
     * @param string $secondaryLogoPath
     * @return Government
     */
    public function setSecondaryLogoPath($secondaryLogoPath)
    {
        $this->secondaryLogoPath = $secondaryLogoPath;

        return $this;
    }

    /**
     * Get secondaryLogoPath
     *
     * @return string
     */
    public function getSecondaryLogoPath()
    {
        return $this->secondaryLogoPath;
    }

    /**
     * @param User $subscriber A User instance.
     *
     * @return Government
     */
    public function addSubscribers(User $subscriber)
    {
        $this->subscribers[] = $subscriber;

        return $this;
    }

    /**
     * @param User $subscriber A User instance.
     *
     * @return Government
     */
    public function removeSubscribers(User $subscriber)
    {
        $this->subscribers->removeElement($subscriber);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @param User $user A User entity.
     *
     * @return boolean
     */
    public function isSubscriber(User $user)
    {
        return $this->subscribers->contains($user);
    }

    /**
     * Add subscribers
     *
     * @param \GovWiki\UserBundle\Entity\User $subscribers
     * @return Government
     */
    public function addSubscriber(\GovWiki\UserBundle\Entity\User $subscribers)
    {
        $this->subscribers[] = $subscribers;

        return $this;
    }

    /**
     * Remove subscribers
     *
     * @param \GovWiki\UserBundle\Entity\User $subscribers
     */
    public function removeSubscriber(\GovWiki\UserBundle\Entity\User $subscribers)
    {
        $this->subscribers->removeElement($subscribers);
    }

    /**
     * Set chat
     *
     * @param \GovWiki\DbBundle\Entity\Chat $chat
     * @return Government
     */
    public function setChat(\GovWiki\DbBundle\Entity\Chat $chat = null)
    {
        $this->chat = $chat;

        return $this;
    }

    /**
     * Get chat
     *
     * @return Chat
     */
    public function getChat()
    {
        return $this->chat;
    }

    /**
     * @param Salary $salary A Salary entity instance.
     *
     * @return Government
     */
    public function addSalaries(Salary $salary)
    {
        $this->salaries[] = $salary;

        return $this;
    }

    /**
     * @param Salary $salary A Salary entity instance.
     *
     * @return Government
     */
    public function removeSalaries(Salary $salary)
    {
        $this->salaries->removeElement($salary);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSalaries()
    {
        return $this->salaries;
    }

    /**
     * @param Pension $pension A Pension entity instance.
     *
     * @return Government
     */
    public function addPensions(Pension $pension)
    {
        $this->pensions[] = $pension;

        return $this;
    }

    /**
     * @param Pension $pension A Pension entity instance.
     *
     * @return Government
     */
    public function removePensions(Pension $pension)
    {
        $this->pensions->removeElement($pension);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPensions()
    {
        return $this->pensions;
    }

    /**
     * @param Issue $issue A Issue entity instance.
     *
     * @return Government
     */
    public function addDocuments(Issue $issue)
    {
        $this->issues[] = $issue;

        return $this;
    }

    /**
     * @param Issue $issue A Issue entity instance.
     *
     * @return Government
     */
    public function removeIssues(Issue $issue)
    {
        $this->issues->removeElement($issue);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image Path to main image.
     *
     * @return Government
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Set comment
     *
     * @param string $comment Free text discussion about government.
     *
     * @return Government
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set webSiteAddress
     *
     * @param string $webSiteAddress
     *
     * @return Government
     */
    public function setWebSiteAddress($webSiteAddress)
    {
        $this->webSiteAddress = $webSiteAddress;

        return $this;
    }

    /**
     * Get webSiteAddress
     *
     * @return string 
     */
    public function getWebSiteAddress()
    {
        return $this->webSiteAddress;
    }
}
