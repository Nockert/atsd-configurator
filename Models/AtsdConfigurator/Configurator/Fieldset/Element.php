<?php

/**
 * Aquatuning Software Development - Configurator - Model - Element
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;



/**
 * Aquatuning Software Development - Configurator - Model - Element
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="atsd_configurators_fieldsets_elements")
 */

class Element extends ModelEntity
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
     * Internal name for further usage
     *
     * @var string   $name
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */

    private $name;



    /**
     * Displayed description for the customer
     *
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", nullable=false)
     */

    private $description;



    /**
     * ...
     *
     * @var string   $mediaFile
     *
     * @ORM\Column(name="mediaFile", type="string")
     */

    private $mediaFile;



    /**
     * The position at which the Fieldset is being displayed
     *
     * @var integer $position
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */

    private $position;



    /**
     * Variable whether the current element is optional or mandatory.
     *
     * @var boolean
     *
     *@ORM\Column(name="mandatory", type="boolean", nullable=false)
     */

    private $mandatory = false;



    /**
     * Variable whether the elements can be selected multiple times
     *
     * @var boolean
     *
     * @ORM\Column(name="multiple", type="boolean", nullable=false)
     */

    private $multiple = false;



    /**
     * Displayed comment on the element (i.e. help)
     *
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="string", nullable=false)
     */

    private $comment;



    /**
     * OWNING SIDE
     *
     * Fieldset model
     *
     * @var \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset $fieldset
     *
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset", inversedBy="elements")
     * @ORM\JoinColumn(name="fieldsetId", referencedColumnName="id")
     */

    protected $fieldset;



    /**
     * Template model
     *
     * OWNING SIDE - UNI DIRECTIONAL
     *
     * @var \Shopware\CustomModels\AtsdConfigurator\Template   $template
     *
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\AtsdConfigurator\Template")
     * @ORM\JoinColumn(name="templateId", referencedColumnName="id")
     */

    protected $template;



    /**
     * INVERSE SIDE
     *
     * (Selfmade) Article model
     *
     * @var ArrayCollection $articles
     *
     * @ORM\OneToMany(targetEntity="Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article", mappedBy="element", orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */

    protected $articles;



    /**
     * Model constructor to set default values.
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element
     */

    public function __construct()
    {
        // set default values
        $this->articles = new ArrayCollection();
    }



    /**
     * Getter method for the property
     *
     * @return int
     */

    public function getId()
    {
        return $this->id;
    }



    /**
     * Getter method for the property
     *
     * @return string
     */

    public function getName()
    {
        return $this->name;
    }



    /**
     * Setter method for the property
     *
     * @param string $name
     *
     * @return void
     */

    public function setName($name)
    {
        $this->name = $name;
    }



    /**
     * Getter method for the property
     *
     * @return string
     */

    public function getDescription()
    {
        return $this->description;
    }



    /**
     * Setter method for the property
     *
     * @param string $description
     *
     * @return void
     */

    public function setDescription($description)
    {
        $this->description = $description;
    }



    /**
     * Getter method for the property.
     *
     * @return string
     */

    public function getMediaFile()
    {
        return $this->mediaFile;
    }



    /**
     * Setter method for the property.
     *
     * @param string $mediaFile
     *
     * @return void
     */

    public function setMediaFile($mediaFile)
    {
        $this->mediaFile = $mediaFile;
    }


    /**
     * Getter method for the property
     *
     * @return integer
     */

    public function getPosition()
    {
        return $this->position;
    }



    /**
     * Setter method for the property
     *
     * @param integer $position
     *
     * @return void
     */

    public function setPosition($position)
    {
        $this->position = $position;
    }



    /**
     * Getter method for the property
     *
     * @return boolean
     */

    public function getMandatory()
    {
        return $this->mandatory;
    }



    /**
     * Setter method for the property
     *
     * @param boolean   $mandatory
     *
     * @return void
     */

    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;
    }



    /**
     * Getter method for the property
     *
     * @return boolean
     */

    public function getMutiple()
    {
        return $this->multiple;
    }



    /**
     * Setter method for the property
     *
     * @param boolean
     *
     * @return void
     */

    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
    }



    /**
     * Getter method for the property
     *
     * @return string
     */

    public function getComment()
    {
        return $this->comment;
    }



    /**
     * Setter method for the property
     *
     * @param string
     *
     * @return void
     */

    public function setComment($comment)
    {
        $this->comment = $comment;
    }



    /**
     * Getter method for the property
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset $fieldset
     */

    public function getFieldset()
    {
        return $this->fieldset;
    }



    /**
     * Setter method for the property
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset $fieldset
     *
     * @return void
     */

    public function setFieldset( $fieldset)
    {
        $this->fieldset = $fieldset;
    }



    /**
     * Setter method for the property.
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Template  $template
     *
     * @return void
     */

    public function setTemplate( $template )
    {
        $this->template = $template;
    }



    /**
     * Getter method for the property.
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Template
     */

    public function getTemplate()
    {
        return $this->template;
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
     * @param \Shopware\Models\Article\Article $article
     *
     * @return void
     */

    public function addArticle(\Shopware\Models\Article\Article $article)
    {
        if ( !( $this->articles->contains( $article ) ) )
            $this->articles->add( $article );
    }



    /**
     * Remove a single entity of the collection
     *
     * @param \Shopware\Models\Article\Article $article
     *
     * @return void
     */

    public function removeArticle(\Shopware\Models\Article\Article $article)
    {
        if ( $this->articles->contains( $article ) )
            $this->articles->removeElement( $article );
    }

}
