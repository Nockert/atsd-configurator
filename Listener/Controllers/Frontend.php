<?php

/**
 * Aquatuning Software Development - Configurator - Listener
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Listener\Controllers;

use Enlight_Event_EventArgs as EventArgs;
use Enlight_Controller_Action as Controller;



/**
 * Aquatuning Software Development - Configurator - Listener
 */

class Frontend
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
     * @var array
     */

    protected $whitelist = array(
        "atsdrma",
        "atsdarticlequestions",
        "atsdarticlenotifications",
        "atsdconfigurator",
        "atsdcartsave",
        "ticket",
        "address",
        "note",
        "account"
    );



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
     * ...
     *
     * @param EventArgs $arguments
     *
     * @return void
     */

    public function onPostDispatch( EventArgs $arguments )
    {
        /* @var $controller Controller */
        $controller     = $arguments->get('subject');
        $request        = $controller->Request();
        $view           = $controller->View();
        $controllerName = $request->getControllerName();

        // is controller in whitelist?
        if( !in_array( strtolower( $controllerName ), $this->whitelist ) )
            // nope
            return;

        // assign the status
        $view->assign( "atsdConfiguratorShopStatus", (boolean) $this->configuration[ "shopStatus" ] );

        // add template dir
        $view->addTemplateDir( $this->viewDir );
    }

}
