<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConcerto
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber\Core;

use Shopware\AtsdConfigurator\Components\Selection\BasketService;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class sAdmin implements \Enlight\Event\SubscriberInterface
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
	 * Main plugin component.
	 *
	 * @var \Shopware\AtsdConfigurator\Components\AtsdConfigurator
	 */

	protected $component;






	/**
	 * ...
	 *
	 * @param \Shopware_Components_Plugin_Bootstrap                    $bootstrap
	 * @param \Shopware\Components\DependencyInjection\Container       $container
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
			'sAdmin::sGetDispatchBasket::after' => "afterGetDispatchBasket"
		);
	}







	/**
	 * Add the weight to the dispatch basket to get correct shipping costs.
	 *
	 * @param \Enlight_Hook_HookArgs   $arguments
	 *
	 * @return array
	 */

	public function afterGetDispatchBasket( \Enlight_Hook_HookArgs $arguments )
	{
	    /* @var $basketService BasketService */
	    $basketService = $this->container->get( "atsd_configurator.selection.basket-service" );

		// get the query
		$basket = $arguments->getReturn();

		// add the weight
        $basket['weight'] = (float) $basket['weight'] + $basketService->getBasketWeight( $basket['sessionID'] );

		// return the correct basket
		return $basket;
	}









}