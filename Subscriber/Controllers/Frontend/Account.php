<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber\Controllers\Frontend;

use Enlight\Event\SubscriberInterface;
use Shopware_Components_Plugin_Bootstrap as Bootstrap;
use Shopware\Components\DependencyInjection\Container;
use Enlight_Event_EventArgs as EventArgs;
use Enlight_Controller_Action as Controller;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class Account implements SubscriberInterface
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
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Account' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatchSecure_Widgets'          => 'onPostDispatch'
		);
	}








    /**
     * Extends the default account menu with our link.
     *
     * @param EventArgs   $arguments
     *
     * @return void
     */

    public function onPostDispatch( EventArgs $arguments )
    {
        // get parameters
        /* @var $controller Controller */
        $controller = $arguments->get( "subject" );
        $view       = $controller->View();

        // add template dir
        $view->addTemplateDir( $this->bootstrap->Path() . "Views/" );

        // assign the status
        $view->assign( "atsdConfiguratorShopStatus", (boolean) $this->bootstrap->Config()->get( "shopStatus" ) );
    }





}