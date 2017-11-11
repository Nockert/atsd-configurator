<?php

/**
 * Aquatuning Software Development - Configurator - Listener
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Listener\Bundle\StoreFrontBundle;

use Shopware\Components\DependencyInjection\Container;
use AtsdConfigurator\Bundle\StoreFrontBundle\ListProductService as PluginService;



/**
 * Aquatuning Software Development - Configurator - Listener
 */

class ListProductService
{

	/**
	 * DI container.
	 *
	 * @var Container
	 */

	protected $container;



	/**
	 * ...
	 *
	 * @param Container   $container
	 */

	public function __construct( Container $container )
	{
		// set params
		$this->container = $container;
	}



    /**
     * ...
     *
     * @return void
     */

    public function afterListProductService()
    {
        // only if we have a session
        if ( !$this->container->initialized( "session" ) )
            // we might be in the backend
            return;

        // get the services
        $coreService         = $this->container->get( "shopware_storefront.list_product_service" );
        $component           = $this->container->get( "atsd_configurator.component" );
        $configuratorService = $this->container->get( "atsd_configurator.bundle.store_front_bundle.configurator_service" );

        // create our own component and set the core service
        $service = new PluginService(
            $coreService,
            $configuratorService,
            $component,
            $this->container
        );

        // and replace it
        $this->container->set( "shopware_storefront.list_product_service", $service );
    }

}
