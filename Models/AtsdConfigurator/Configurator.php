<?php

/**
 * Aquatuning Software Development - Configurator - Model
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
 * Aquatuning Software Development - Configurator - Model
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="atsd_configurators")
 */

class Configurator extends ModelEntity
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
     * Status if active or inactive.
     *
     * @var boolean   $status
     *
     * @ORM\Column(name="status", type="boolean")
     */

    private $status = true;



    /**
     * Internal name for further usage
     *
     * @var string   $name
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */

    private $name;



    /**
     * Rebate of the configurator.
     *
     * @var integer   $rebate
     *
     * @ORM\Column(name="rebate", type="integer", nullable=false)
     */

    private $rebate = 0;



    /**
     * ...
     *
     * @var boolean   $chargeArticle
     *
     * @ORM\Column(name="chargeArticle", type="boolean", nullable=false, options={"default":1})
     **/

    private $chargeArticle = true;



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
     * INVERSE SIDE
     *
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset", mappedBy="configurator", orphanRemoval=true)
     */

    protected $fieldsets;



    /**
     * Model constructor to set default values.
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Configurator
     */

    public function __construct()
    {
        // set default values
        $this->fieldsets = new ArrayCollection();
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
     * @param boolean   $status
     *
     * @return void
     */

    public function setStatus( $status )
    {
        $this->status = $status;
    }



    /**
     * Getter method for the property.
     *
     * @return boolean
     */

    public function getStatus()
    {
        return $this->status;
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
     * Getter method for the property.
     *
     * @return int
     */
    public function getRebate()
    {
        return $this->rebate;
    }

    /**
     * Setter method for the property.
     *
     * @param int $rebate
     *
     * @return void
     */
    public function setRebate($rebate)
    {
        $this->rebate = $rebate;
    }

    /**
     * Getter method for the property.
     *
     * @return boolean
     */
    public function getChargeArticle()
    {
        return $this->chargeArticle;
    }

    /**
     * Setter method for the property.
     *
     * @param boolean $chargeArticle
     *
     * return void
     */
    public function setChargeArticle($chargeArticle)
    {
        $this->chargeArticle = $chargeArticle;
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

    public function setArticle($article)
    {
        $this->article = $article;
    }


    /**
     * Getter method for the property
     *
     * @return ArrayCollection
     */

    public function getFieldsets()
    {
        return $this->fieldsets;
    }


    /**
     * Setter method for the property
     *
     * @param ArrayCollection $fieldsets
     *
     * @return void
     */

    public function setFieldsets($fieldsets)
    {
        $this->fieldsets = $fieldsets;
    }


    /**
     * Add a single entity to the collection
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset $fieldset
     *
     * @return void
     */

    public function addFieldset(\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset $fieldset)
    {
        // Not included?
        if (!($this->fieldsets->contains( $fieldset )))
            // add it
            $this->fieldsets->add( $fieldset );
    }


    /**
     * Remove a single entity from the collection
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset $fieldset
     *
     * @return void
     */

    public function removeFieldset(\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset $fieldset)
    {
        // Included?
        if ( $this->fieldsets->contains( $fieldset ) )
            // remove it
            $this->fieldsets->removeElement( $fieldset );
    }

}
?>