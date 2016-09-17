<?php

/**
 * Aquatuning Software Development - Configurator - Article
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;



/**
 * Aquatuning Software Development - Configurator - Article
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="atsd_configurators_fieldsets_elements_articles")
 */

class Article extends ModelEntity
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
     * The position at which the Article is being displayed
     *
     * @var integer $position
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */

    private $position;

    /**
     * The quantity of the articles
     *
     * @var integer $quantity
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */

    private $quantity;


    /**
     * OWNING SIDE
     *
     * Element model
     *
     * @var \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element $element
     *
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element", inversedBy="articles")
     * @ORM\JoinColumn(name="elementId", referencedColumnName="id")
     */

    protected $element;


    /**
     * Article model
     *
     * @var \Shopware\Models\Article\Article   $article
     *
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Article\Article")
     * @ORM\JoinColumn(name="articleId", referencedColumnName="id")
     */

    protected $article;


    /**
     * Model constructor to set default values.
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article
     *
     */

    public function __construct()
    {
        // set default values
        $this->articles = new ArrayCollection();
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
     * @return integer
     */

    public function getQuantity()
    {
        return $this->quantity;
    }


    /**
     * Setter method for the property
     *
     * @param integer
     *
     * @return void
     */

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }


    /**
     * Getter method for the property
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element $element
     */

    public function getElement()
    {
        return $this->element;
    }

    /**
     * Setter method for the property
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element $element
     *
     * @return void
     */

    public function setElement(\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element $element)
    {
        $this->element = $element;
    }


    /**
     * Getter method for the property
     *
     * @return \Shopware\Models\Article\Article
     */

    public function getArticle()
    {
        return $this->article;
    }


    /**
     * Setter method for the property
     *
     * @param \Shopware\Models\Article\Article $article
     *
     * @return void
     */

    public function setArticle(\Shopware\Models\Article\Article $article)
    {
        $this->article = $article;
    }

}
?>