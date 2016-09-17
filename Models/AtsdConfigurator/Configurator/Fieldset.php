<?php

/**
 * Aquatuning Software Development - Configurator - Model - Fieldset
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\CustomModels\AtsdConfigurator\Configurator;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;



/**
 * Aquatuning Software Development - Configurator - Model - Fieldset
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="atsd_configurators_fieldsets")
 */

class Fieldset extends ModelEntity
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
     * The position at which the Fieldset is being diaplyed
     *
     * @var integer $position
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */

    private $position;



    /**
     * OWNING SIDE
     *
     * The main configurator
     *
     * @var \Shopware\CustomModels\AtsdConfigurator\Configurator $configurator
     *
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\AtsdConfigurator\Configurator", inversedBy="fieldsets")
     * @ORM\JoinColumn(name="configuratorId", referencedColumnName="id")
     */

    protected $configurator;



    /**
     * INVERSE SIDE
     *
     * Element Model
     *
     * @var ArrayCollection $elements
     *
     * @ORM\OneToMany(targetEntity="Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element", mappedBy="fieldset", orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */

    protected $elements;



    /**
     * Model constructor to set default values.
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset
     */

    public function __construct()
    {
        // set default values
        $this->elements = new ArrayCollection();
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

    public function getElements()
    {
        return $this->elements;
    }


    /**
     * Setter method for the property
     *
     * @param ArrayCollection $elements
     *
     * @return void
     */

    public function setElements($elements)
    {
        $this->elements = $elements;
    }


    /**
     * Add a single entity to the collection
     *
     * @param Fieldset\Element $element
     *
     * @return void
     */

    public function addElement(\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element $element)
    {
        if ( !( $this->elements->contains( $element ) ) )
            $this->elements->add( $element );
    }


    /**
     * Remove a single entity from the collection
     *
     * @param Fieldset\Element $element
     *
     * @return void
     */

    public function removeElement(\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element $element)
    {
        if($this->elements->contains( $element ) )
            $this->elements->removeElement( $element );
    }
}
