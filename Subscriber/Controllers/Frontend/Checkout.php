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

class Checkout implements \Enlight\Event\SubscriberInterface
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
     * The main component.
     *
     * @var \Shopware\AtsdConfigurator\Components\AtsdConfigurator
     */

    private $component;



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
	 * @return \Shopware\AtsdConfigurator\Subscriber\Controllers\Frontend\Checkout
	 */

    public function __construct( \Shopware_Components_Plugin_Bootstrap $bootstrap, \Shopware\Components\DependencyInjection\Container $container )
	{
        // set params
        $this->bootstrap = $bootstrap;
        $this->container = $container;

        // set container params
        $this->component    = $this->container->get( "atsd_configurator.component" );
        $this->modelManager = $this->container->get( "shopware.model_manager" );
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
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => "onPostDispatch"
		);
	}








    /**
     * ...
     *
     * @param \Enlight_Event_EventArgs   $arguments
     *
     * @return void
     */

    public function onPostDispatch ( \Enlight_Event_EventArgs $arguments )
    {
        // get the controller
        /* @var $controller \Shopware_Controllers_Frontend_Checkout */
        $controller = $arguments->get( "subject" );

        // get parameters
        $request    = $controller->Request();
        $response   = $controller->Response();
        $view       = $controller->View();

        // only these actions
        if ( !in_array( strtolower( $request->getActionName() ), array( "cart", "confirm", "finish" ) ) )
            // nothing to do
            return;

        // add our template dir
        $view->addTemplateDir( $this->bootstrap->Path() . "Views/" );

        // assign the status
        $view->assign( "atsdConfiguratorShopStatus", (boolean) $this->bootstrap->Config()->get( "shopStatus" ) );

        // and we are done
        return;
    }






}