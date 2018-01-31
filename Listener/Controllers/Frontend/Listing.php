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
use Shopware_Controllers_Frontend_Listing as Controller;



/**
 * Aquatuning Software Development - Configurator - Listener
 */

class Listing
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
     * ...
     *
     * @param EventArgs   $arguments
     *
     * @return void
     */

    public function onPostDispatch ( EventArgs $arguments )
    {
        // ...
        /* @var $controller Controller */
        $controller = $arguments->get( "subject" );
        $view       = $controller->View();

        // add our template dir
        $view->addTemplateDir( $this->viewDir );

        // assign the status
        $view->assign( "atsdConfiguratorShopStatus", (boolean) $this->configuration['shopStatus'] );
    }

}
