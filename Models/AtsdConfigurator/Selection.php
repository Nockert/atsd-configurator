<?php

/**
 * Aquatuning Software Development - Configurator - Model - Selection
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\CustomModels\AtsdConfigurator;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;



/**
 * Aquatuning Software Development - Configurator - Model - Selection
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="atsd_configurators_selections")
 */

class Selection extends ModelEntity
{

    /**
     * Auto-generated id.
     *
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */

    private $id;



    /**
     * The date of the creation of the selector.
     *
     * @var \DateTime   $date
     *
     * @Assert\DateTime()
     *
     * @ORM\Column(name="`date`", type="datetime")
     */

    private $date;



    /**
     * A unique key to load the selector.
     *
     * @var string   $key
     *
     * @ORM\Column(name="`key`", type="string", length=32, nullable=false)
     **/

    private $key;



    /**
     * Did we manually save the selection?
     *
     * @var boolean   $manual
     *
     * @ORM\Column(name="`manual`", type="boolean")
     **/

    private $manual = false;



    /**
     * OWNING SIDE - UNI DIRECTIONAL
     *
     * The customer who saved this selection.
     *
     * @var \Shopware\Models\Customer\Customer
     *
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Customer\Customer")
     * @ORM\JoinColumn(name="customerId", referencedColumnName="id")
     */

    protected $customer;



    /**
     * OWNING SIDE
     *
     * The main configurator model
     *
     * @var \Shopware\CustomModels\AtsdConfigurator\Configurator $configurator
     *
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\AtsdConfigurator\Configurator")
     * @ORM\JoinColumn(name="configuratorId", referencedColumnName="id")
     */

    protected $configurator;



    /**
     * UNI-DIRECTIONAL
     *
     * Article Model
     *
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article")
     * @ORM\JoinTable(name="atsd_configurators_selections_to_articles",
     *      joinColumns={
     *          @ORM\JoinColumn(name="selectionId", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="articleId", referencedColumnName="id")
     *      }
     * )
     */

    protected $articles;





    /**
     * Model constructor to set default values.
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Selection
     */

    public function __construct()
    {
        // set default values
        $this->date     = new \DateTime();
        $this->articles = new ArrayCollection();

        // set random selector key
        $this->key      = substr( md5( time() . microtime() . rand() ), 0, 32 );
    }





    /**
     * Getter method for the property
     *
     * @return integer
     */


    public function getId()
    {
        return $this->id;
    }



    /**
     * Setter method for the property.
     *
     * @param \DateTime|string   $date
     *
     * @return void
     */

    public function setDate( $date = "now" )
    {
        $this->date = ( !( $date instanceof \DateTime ) )
            ? new \DateTime( $date )
            : $date;
    }



    /**
     * Getter method for the property.
     *
     * @return \DateTime
     */

    public function getDate()
    {
        return $this->date;
    }



    /**
     * Getter method for the property
     *
     * @return string
     */

    public function getKey()
    {
        return $this->key;
    }



    /**
     * Setter method for the property
     *
     * @param string   $key
     *
     * @return void
     */

    public function setKey( $key )
    {
        $this->key = $key;
    }



    /**
     * Getter method for the property.
     *
     * @return boolean
     */

    public function getManual()
    {
        return $this->manual;
    }



    /**
     * Setter method for the property.
     *
     * @param boolean $manual
     *
     * @return void
     */

    public function setManual($manual)
    {
        $this->manual = $manual;
    }



    /**
     * Setter method for the property.
     *
     * @param \Shopware\Models\Customer\Customer   $customer
     *
     * @return void
     */

    public function setCustomer( $customer )
    {
        $this->customer = $customer;
    }



    /**
     * Getter method for the property.
     *
     * @return \Shopware\Models\Customer\Customer
     */

    public function getCustomer()
    {
        return $this->customer;
    }




    /**
     * Getter method for the property.
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Configurator $configurator
     */

    public function getConfigurator()
    {
        return $this->configurator;
    }



    /**
     * Setter method for the property
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Configurator $configurator
     *
     * @return void
     */

    public function setConfigurator(\Shopware\CustomModels\AtsdConfigurator\Configurator $configurator)
    {
        $this->configurator = $configurator;
    }



    /**
     * Getter method for the property
     *
     * @return ArrayCollection
     */

    public function getArticles()
    {
        return $this->articles;
    }



    /**
     * Setter method for the property
     *
     * @param ArrayCollection $articles
     *
     * @return void
     */

    public function setArticles($articles)
    {
        $this->articles = $articles;
    }



    /**
     * Add a single entity to the collection
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article $article
     *
     * @return void
     */

    public function addArticle(\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article $article)
    {
        if ( !( $this->articles->contains( $article ) ) )
            $this->articles->add( $article );
    }



    /**
     * Remove a single entity of the collection
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article $article
     *
     * @return void
     */

    public function removeArticle(\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article $article)
    {
        if ( $this->articles->contains( $article ) )
            $this->articles->removeElement( $article );
    }



}
