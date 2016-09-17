<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components;

use Shopware\Bundle\StoreFrontBundle;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class AtsdConfigurator
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
     * @return \Shopware\AtsdConfigurator\Components\AtsdConfigurator
     */

    public function __construct( \Shopware_Components_Plugin_Bootstrap $bootstrap, \Shopware\Components\DependencyInjection\Container $container )
    {
        // set params
        $this->bootstrap = $bootstrap;
        $this->container = $container;

        // set internal params
        $this->modelManager = $this->container->get( "shopware.model_manager" );
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



