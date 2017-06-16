<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber\Controllers\Frontend;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class Account implements \Enlight\Event\SubscriberInterface
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
            'Enlight_Controller_Action_PostDispatch_Frontend_Account' => 'onPostDispatchFrontendAccount'
		);
	}








    /**
     * Extends the default account menu with our link.
     *
     * @param \Enlight_Event_EventArgs   $arguments
     *
     * @return void
     */

    public function onPostDispatchFrontendAccount( \Enlight_Event_EventArgs $arguments )
    {
        // get parameters
        /* @var $controller \Shopware_Controllers_Frontend_Account */
        $controller = $arguments->get( "subject" );
        $request    = $controller->Request();
        $response   = $controller->Response();
        $view       = $controller->View();
        $action     = $request->getActionName();

        // valid request?
        if ( !$request->isDispatched() || $response->isException() || !$view->hasTemplate() || $request->getModuleName() != "frontend" )
            // abort
            return;

        // add template dir
        $view->addTemplateDir( $this->bootstrap->Path() . "Views/" );

        // assign the status
        $view->assign( "atsdConfiguratorShopStatus", (boolean) $this->bootstrap->Config()->get( "shopStatus" ) );
    }





}