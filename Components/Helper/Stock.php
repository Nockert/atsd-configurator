<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components\Helper;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class Stock
{


    /**
     * Main bootstrap object.
     *
     * @var \Shopware_Components_Plugin_Bootstrap
     */

    protected $bootstrap;



    /**
     * DI container.
     *
     * @var \Shopware\Components\DependencyInjection\Container
     */

    protected $container;





    /**
     * ...
     *
     * @param \Shopware_Components_Plugin_Bootstrap                $bootstrap
     * @param \Shopware\Components\DependencyInjection\Container   $container
     *
     * @return \Shopware\AtsdConfigurator\Components\Helper\Stock
     */

    public function __construct( \Shopware_Components_Plugin_Bootstrap $bootstrap, \Shopware\Components\DependencyInjection\Container $container )
    {
        // set params
        $this->bootstrap = $bootstrap;
        $this->container = $container;
    }






    /**
     * Returns the config.
     *
     * @return \Enlight_Config
     */

    protected function getConfig()
    {
        // return it
        return $this->bootstrap->Config();
    }










}



