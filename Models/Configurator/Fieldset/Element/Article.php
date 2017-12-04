<?php

/**
 * Aquatuning Software Development - Configurator - Article
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Models\Configurator\Fieldset\Element;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;
use AtsdConfigurator\Models\Configurator\Fieldset\Element;


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
     * ...
     *
     * @var boolean   $quantitySelect
     *
     * @ORM\Column(name="quantitySelect", type="boolean", nullable=false, options={"default":0})
     **/

    private $quantitySelect = false;

    /**
     * ...
     *
     * @var boolean   $quantityMultiply
     *
     * @ORM\Column(name="quantityMultiply", type="boolean", nullable=false, options={"default":0})
     **/

    private $quantityMultiply = false;

    /**
     * ...
     *
     * @var boolean
     *
     * @ORM\Column(name="surcharge", type="integer", nullable=false, options={"default":0})
     **/

    private $surcharge = 0;

    /**
     * OWNING SIDE
     *
     * Element model
     *
     * @var Element $element
     *
     * @ORM\ManyToOne(targetEntity="AtsdConfigurator\Models\Configurator\Fieldset\Element", inversedBy="articles")
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
     * Getter method for the property.
     *
     * @return bool
     */
    public function getQuantitySelect()
    {
        return $this->quantitySelect;
    }

    /**
     * Setter method for the property.
     *
     * @param bool $quantitySelect
     *
     * return void
     */
    public function setQuantitySelect($quantitySelect)
    {
        $this->quantitySelect = $quantitySelect;
    }

    /**
     * Getter method for the property.
     *
     * @return bool
     */
    public function getQuantityMultiply()
    {
        return $this->quantityMultiply;
    }

    /**
     * Setter method for the property.
     *
     * @param bool $quantityMultiply
     *
     * return void
     */
    public function setQuantityMultiply($quantityMultiply)
    {
        $this->quantityMultiply = $quantityMultiply;
    }

    /**
     * Getter method for the property.
     *
     * @return bool
     */
    public function getSurcharge()
    {
        return $this->surcharge;
    }

    /**
     * Setter method for the property.
     *
     * @param bool $surcharge
     *
     * return void
     */
    public function setSurcharge($surcharge)
    {
        $this->surcharge = $surcharge;
    }



    /**
     * Getter method for the property
     *
     * @return Element $element
     */

    public function getElement()
    {
        return $this->element;
    }

    /**
     * Setter method for the property
     *
     * @param Element $element
     *
     * @return void
     */

    public function setElement(Element $element)
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
