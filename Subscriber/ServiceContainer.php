<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConcerto
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class ServiceContainer implements \Enlight\Event\SubscriberInterface
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
	 * @return \Shopware\AtsdConfigurator\Subscriber\ServiceContainer
	 */

	public function __construct( \Shopware_Components_Plugin_Bootstrap $bootstrap, \Shopware\Components\DependencyInjection\Container $container )
	{
		// set params
		$this->bootstrap = $bootstrap;
        $this->container = $container;
	}






	/**
	 * Return the subscribed controller events.
	 *
	 * @return array
	 */

	public static function getSubscribedEvents()
	{
		// return the events
		return array(
            'Enlight_Bootstrap_InitResource_atsd_configurator.bootstrap'                   => "getBootstrap",
            'Enlight_Bootstrap_InitResource_atsd_configurator.component'                   => "getComponent",
            'Enlight_Bootstrap_InitResource_atsd_configurator.configurator_service'        => "getService",
            'Enlight_Bootstrap_AfterInitResource_shopware_storefront.list_product_service' => "afterListProductService",
		);
	}





    /**
     * ...
     *
     * @return \Shopware_Components_Plugin_Bootstrap
     */

    public function getBootstrap()
    {
        // ...
        return $this->bootstrap;
    }




    /**
     * ...
     *
     * @return \Shopware\AtsdConfigurator\Components\AtsdConfigurator
     */

    public function getComponent()
    {
        // ...
        return new \Shopware\AtsdConfigurator\Components\AtsdConfigurator(
            $this->bootstrap,
            $this->container
        );
    }








    /**
     * ...
     *
     * @return \Shopware\AtsdConfigurator\Bundle\StoreFrontBundle\ConfiguratorService
     */

    public function getService()
    {
        // ...
        return new \Shopware\AtsdConfigurator\Bundle\StoreFrontBundle\ConfiguratorService(
            $this->container->get( "shopware.model_manager" ),
            $this->container->get( "atsd_configurator.component" ),
            $this->container
        );
    }





    /**
     * ...
     *
     * @return void
     */

    public function afterListProductService()
    {
        // get the services
        $coreService         = $this->container->get( "shopware_storefront.list_product_service" );
        $component           = $this->container->get( "atsd_configurator.component" );
        $configuratorService = $this->container->get( "atsd_configurator.configurator_service" );

        // create our own component and set the core service
        $service = new \Shopware\AtsdConfigurator\Bundle\StoreFrontBundle\ListProductService(
            $coreService,
            $configuratorService,
            $component,
            $this->container
        );

        // and replace it
        $this->container->set( "shopware_storefront.list_product_service", $service );

        // done
        return;
    }





}