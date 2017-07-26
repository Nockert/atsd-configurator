<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConcerto
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber\Plugins\AtsdShippingUpsXml;

use Enlight\Event\SubscriberInterface;
use Shopware_Components_Plugin_Bootstrap as Bootstrap;
use Shopware\Components\DependencyInjection\Container;
use Shopware\AtsdConfigurator\Components\Selection\BasketService;
use Enlight_Event_EventArgs as EventArgs;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class ModifyWeight implements SubscriberInterface
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
			'Shopware_AtsdShippingUpsXml_ModifyWeight' => "onModifyWeight"
		);
	}







	/**
	 * ...
	 *
	 * @param EventArgs   $arguments
	 *
	 * @return float
	 */

	public function onModifyWeight( EventArgs $arguments )
	{
	    /* @var $basketService BasketService */
	    $basketService = $this->container->get( "atsd_configurator.selection.basket-service" );

		// get the weight
		$weight = $arguments->getReturn();

		// add our weight
        $weight = (float) $weight + $basketService->getBasketWeight( $this->getSessionId() );

		// return the correct weight
		return $weight;
	}







    /**
     * ...
     *
     * @return string
     */

    private function getSessionId()
    {
        // return via context service
        return $this->container->get( "session" )->get( "sessionId" );
    }






}