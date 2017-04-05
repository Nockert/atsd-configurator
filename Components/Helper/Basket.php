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

class Basket
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
     * Shopware context service.
     *
     * @var \Shopware\AtsdConfigurator\Components\Helper\Price
     */

    protected $priceHelper;



    /**
     * Shopware model manager.
     *
     * @var \Shopware\Components\Model\ModelManager
     */

    protected $modelManager;





    /**
     * ...
     *
     * @param \Shopware_Components_Plugin_Bootstrap                $bootstrap
     * @param \Shopware\Components\DependencyInjection\Container   $container
     *
     * @return Basket
     */

    public function __construct( \Shopware_Components_Plugin_Bootstrap $bootstrap, \Shopware\Components\DependencyInjection\Container $container )
    {
        // set params
        $this->bootstrap = $bootstrap;
        $this->container = $container;

        // set internal params
        $this->priceHelper  = $this->container->get( "atsd_configurator.helper.price" );
        $this->modelManager = $this->container->get( "shopware.model_manager" );
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






    /**
     * Returns the current session.
     *
     * @return \Enlight_Components_Session_Namespace
     */

    protected function getSession()
    {
        // return it
        return $this->container->get( "session" );
    }




}



