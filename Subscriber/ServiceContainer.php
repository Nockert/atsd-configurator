<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConcerto
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber;

use Shopware_Plugins_Frontend_AtsdConfigurator_Bootstrap as Bootstrap;
use Shopware\Components\DependencyInjection\Container;
use Enlight\Event\SubscriberInterface;
use Shopware\AtsdConfigurator\Components;
use Enlight_Config as Config;
use Shopware\AtsdConfigurator\Bundle as PluginBundle;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class ServiceContainer implements SubscriberInterface
{

	/**
	 * Main bootstrap object.
	 *
	 * @var Bootstrap
	 */

	protected $bootstrap;



    /**
     * DI container.
     *
     * @var Container
     */

    protected $container;





    /**
	 * ...
	 *
	 * @param Bootstrap   $bootstrap
     * @param Container   $container
	 *
	 * @return ServiceContainer
	 */

	public function __construct( Bootstrap $bootstrap, Container $container )
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
            'Enlight_Bootstrap_InitResource_atsd_configurator.bootstrap'                          => "getBootstrap",
            'Enlight_Bootstrap_InitResource_atsd_configurator.config'                             => "getConfig",
            'Enlight_Bootstrap_InitResource_atsd_configurator.component'                          => "getComponent",
            'Enlight_Bootstrap_InitResource_atsd_configurator.version-service'                    => "getVersionService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.configurator.article-price-service' => "getConfiguratorArticlePriceService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.configurator.filter-service'        => "getConfiguratorFilterService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.configurator.parser-service'        => "getConfiguratorParserService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.configurator.stock-service'         => "getConfiguratorStockService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.configurator.validator-service'     => "getConfiguratorValidatorService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.helper.customer-service'            => "getHelperCustomerService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.selection.basket-service'           => "getSelectionBasketService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.selection.calculator-service'       => "getSelectionCalculatorService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.selection.creator-service'          => "getSelectionCreatorService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.selection.default-service'          => "getSelectionDefaultService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.selection.parser-service'           => "getSelectionParserService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.selection.validator-service'        => "getSelectionValidatorService",
            'Enlight_Bootstrap_InitResource_atsd_configurator.bundle.configurator_service'        => "getBundleService",
            'Enlight_Bootstrap_AfterInitResource_shopware_storefront.list_product_service'        => "afterListProductService",
		);
	}






    /**
     * ...
     *
     * @return Components\Selection\ValidatorService
     */

    public function getSelectionValidatorService()
    {
        // ...
        return new Components\Selection\ValidatorService();
    }






    /**
     * ...
     *
     * @return Components\Selection\ParserService
     */

    public function getSelectionParserService()
    {
        // ...
        return new Components\Selection\ParserService(
            $this->container->get( "shopware.model_manager" )->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator' ),
            $this->container->get( "atsd_configurator.configurator.filter-service" ),
            $this->container->get( "atsd_configurator.configurator.parser-service" ),
            $this->container->get( "atsd_configurator.selection.validator-service" )
        );
    }





    /**
     * ...
     *
     * @return Components\Selection\DefaultService
     */

    public function getSelectionDefaultService()
    {
        // ...
        return new Components\Selection\DefaultService(
            $this->container->get( "shopware.model_manager" )->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator' )
        );
    }






    /**
     * ...
     *
     * @return Components\Selection\CreatorService
     */

    public function getSelectionCreatorService()
    {
        // ...
        return new Components\Selection\CreatorService(
            $this->container->get( "shopware.model_manager" ),
            $this->container->get( "atsd_configurator.helper.customer-service" )
        );
    }






    /**
     * ...
     *
     * @return Components\Selection\CalculatorService
     */

    public function getSelectionCalculatorService()
    {
        // ...
        return new Components\Selection\CalculatorService(
            $this->container->get( "atsd_configurator.selection.parser-service" ),
            $this->container->get( "atsd_configurator.configurator.article-price-service" )
        );
    }





    /**
     * ...
     *
     * @return Components\Selection\BasketService
     */

    public function getSelectionBasketService()
    {
        // ...
        return new Components\Selection\BasketService(
            $this->container->get( "shopware.model_manager" ),
            $this->container->get( "session" ),
            $this->container->get( "shopware_storefront.context_service" ),
            $this->container->get( "atsd_configurator.component" )
        );
    }







    /**
     * ...
     *
     * @return Components\Helper\CustomerService
     */

    public function getHelperCustomerService()
    {
        // ...
        return new Components\Helper\CustomerService(
            $this->container->get( "session" ),
            $this->container->get( "shopware.model_manager" )
        );
    }





    /**
     * ...
     *
     * @return Components\Configurator\ValidatorService
     */

    public function getConfiguratorValidatorService()
    {
        // ...
        return new Components\Configurator\ValidatorService(
            $this->container->get( "atsd_configurator.configurator.stock-service" )
        );
    }





    /**
     * ...
     *
     * @return Components\Configurator\StockService
     */

    public function getConfiguratorStockService()
    {
        // ...
        return new Components\Configurator\StockService(
            $this->container->get( "atsd_configurator.config" )
        );
    }




    /**
     * ...
     *
     * @return Components\Configurator\ParserService
     */

    public function getConfiguratorParserService()
    {
        // ...
        return new Components\Configurator\ParserService(
            $this->container->get( "atsd_configurator.version-service" ),
            $this->container->get( "shopware_storefront.list_product_service" ),
            $this->container->get( "shopware_storefront.context_service" ),
            $this->container->get( "shopware_media.media_service" )
        );
    }





    /**
     * ...
     *
     * @return Components\Configurator\FilterService
     */

    public function getConfiguratorFilterService()
    {
        // ...
        return new Components\Configurator\FilterService(
            $this->container->get( "atsd_configurator.config" ),
            $this->container->get( "atsd_configurator.configurator.stock-service" )
        );
    }





    /**
     * ...
     *
     * @return Components\Configurator\ArticlePriceService
     */

    public function getConfiguratorArticlePriceService()
    {
        // ...
        return new Components\Configurator\ArticlePriceService();
    }






    /**
     * ...
     *
     * @return Components\VersionService
     */

    public function getVersionService()
    {
        // ...
        return new Components\VersionService();
    }





    /**
     * ...
     *
     * @return Bootstrap
     */

    public function getBootstrap()
    {
        // ...
        return $this->bootstrap;
    }




    /**
     * ...
     *
     * @return Config
     */

    public function getConfig()
    {
        // ...
        return $this->bootstrap->Config();
    }




    /**
     * ...
     *
     * @return Components\AtsdConfigurator
     */

    public function getComponent()
    {
        // ...
        return new Components\AtsdConfigurator(
            $this->container,
            $this->container->get( "shopware.model_manager" ),
            (boolean) $this->bootstrap->Config()->get( "cacheStatus" ),
            (integer) $this->bootstrap->Config()->get( "cacheTime" )
        );
    }








    /**
     * ...
     *
     * @return PluginBundle\StoreFrontBundle\ConfiguratorService
     */

    public function getBundleService()
    {
        // ...
        return new PluginBundle\StoreFrontBundle\ConfiguratorService(
            $this->container->get( "shopware.model_manager" )->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator' )
        );
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
        $configuratorService = $this->container->get( "atsd_configurator.bundle.configurator_service" );

        // create our own component and set the core service
        $service = new PluginBundle\StoreFrontBundle\ListProductService(
            $coreService,
            $configuratorService,
            $component,
            $this->container
        );

        // and replace it
        $this->container->set( "shopware_storefront.list_product_service", $service );
    }





}