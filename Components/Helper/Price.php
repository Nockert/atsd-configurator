<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components\Helper;

use Shopware\Bundle\StoreFrontBundle;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class Price
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
     * @var StoreFrontBundle\Service\Core\ContextService
     */

    protected $contextService;





    /**
     * ...
     *
     * @param \Shopware_Components_Plugin_Bootstrap                $bootstrap
     * @param \Shopware\Components\DependencyInjection\Container   $container
     *
     * @return \Shopware\AtsdConfigurator\Components\Helper\Price
     */

    public function __construct( \Shopware_Components_Plugin_Bootstrap $bootstrap, \Shopware\Components\DependencyInjection\Container $container )
    {
        // set params
        $this->bootstrap = $bootstrap;
        $this->container = $container;

        // set internal params
        $this->contextService = $this->container->get( "shopware_storefront.context_service" );
    }









}



