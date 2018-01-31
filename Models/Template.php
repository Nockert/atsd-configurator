<?php

/**
 * Aquatuning Software Development - Configurator - Model - Template
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Models;

use Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Doctrine\Common\Collections\ArrayCollection;



/**
 * Aquatuning Software Development - Configurator - Model - Template
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="atsd_configurators_templates")
 */

class Template extends ModelEntity
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
     * The key for the used template design
     *
     * @var string $key
     *
     * @ORM\Column(name="`key`", type="string", nullable=false)
     */

    private $key;



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
     * @return string
     */

    public function getName()
    {
        return $this->name;
    }



    /**
     * Setter method for the property
     *
     * @param string
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



}


