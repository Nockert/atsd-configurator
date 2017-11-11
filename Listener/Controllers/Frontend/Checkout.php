<?php

/**
 * Aquatuning Software Development - Configurator - Listener
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Listener\Controllers\Frontend;

use Enlight_Event_EventArgs as EventArgs;
use Shopware_Controllers_Frontend_Checkout as Controller;



/**
 * Aquatuning Software Development - Configurator - Listener
 */

class Checkout
{

	/**
	 * ...
	 *
	 * @var string
	 */

	protected $viewDir;



    /**
     * ...
     *
     * @var array
     */

    protected $configuration;



    /**
	 * ...
	 *
	 * @param string   $viewDir
     * @param array    $configuration
	 */

	public function __construct( $viewDir, array $configuration )
	{
		// set params
		$this->viewDir       = $viewDir;
		$this->configuration = $configuration;
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
        // get the controller
        /* @var $controller Controller */
        $controller = $arguments->get( "subject" );

        // get parameters
        $request    = $controller->Request();
        $view       = $controller->View();

        // only these actions
        if ( !in_array( strtolower( $request->getActionName() ), array( "cart", "confirm", "finish" ) ) )
            // nothing to do
            return;

        // add template dir
        $view->addTemplateDir( $this->viewDir );

        // assign the status
        $view->assign( "atsdConfiguratorShopStatus", (boolean) $this->configuration[ "shopStatus" ] );
    }

}
