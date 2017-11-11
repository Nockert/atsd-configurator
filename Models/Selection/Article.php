<?php

/**
 * Aquatuning Software Development - Configurator - Article
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Models\Selection;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;
use AtsdConfigurator\Models\Selection;



/**
 * Aquatuning Software Development - Configurator - Article
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="atsd_configurators_selections_articles")
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
     * The quantity of the articles
     *
     * @var integer $quantity
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */

    private $quantity;

    /**
     * OWNING SIDE - BI DIRECTIONAL
     *
     * ...
     *
     * @var Selection $selection
     *
     * @ORM\ManyToOne(targetEntity="AtsdConfigurator\Models\Selection", inversedBy="articles")
     * @ORM\JoinColumn(name="selectionId", referencedColumnName="id")
     */

    protected $selection;


    /**
     * UNI DIRECTIONAL
     *
     * ...
     *
     * @var \AtsdConfigurator\Models\Configurator\Fieldset\Element\Article   $article
     *
     * @ORM\ManyToOne(targetEntity="AtsdConfigurator\Models\Configurator\Fieldset\Element\Article")
     * @ORM\JoinColumn(name="articleId", referencedColumnName="id")
     */

    protected $article;



    /**
     * Model constructor to set default values.
     *
     */

    public function __construct()
    {
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
     * Getter method for the property.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Setter method for the property.
     *
     * @param int $quantity
     *
     * return void
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Getter method for the property.
     *
     * @return Selection
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * Setter method for the property.
     *
     * @param Selection $selection
     *
     * return void
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }

    /**
     * Getter method for the property.
     *
     * @return \AtsdConfigurator\Models\Configurator\Fieldset\Element\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Setter method for the property.
     *
     * @param \AtsdConfigurator\Models\Configurator\Fieldset\Element\Article $article
     *
     * return void
     */
    public function setArticle($article)
    {
        $this->article = $article;
    }




}

