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
use Enlight_Controller_Action as Controller;

class Sidebar
{
    /**
     * plugin view directory
     *
     * @var $viewDir string
     */
    protected $viewDir;



    /**
     * controller whitelist
     *
     * @var $whitelist array
     */
    protected $whitelist;


    /**
     * @var $configuration array
     */
    protected $configuration;


    /**
     * ...
     *
     * @param string $viewDir
     * @param array $configuration
     */
    public function __construct( $viewDir, $configuration )
    {
        //set params
        $this->viewDir = $viewDir;
        $this->configuration = $configuration;
        $this->whitelist = array(
            "AtsdRma",
            "AtsdArticleQuestions",
            "AtsdArticleNotifications",
            "AtsdConfigurator",
            "AtsdCartSave",
            "ticket",
            "address",
            "note",
            "account"
        );
    }



    /**
     * extend account sidebar
     *
     * @param EventArgs $arguments
     */
    public function onPostDispatch( EventArgs $arguments )
    {
        /* @var $controller Controller */
        $controller = $arguments->get('subject');
        $request = $controller->Request();
        $view = $controller->View();

        $controllerName = $request->getControllerName();

        // is controller in whitelist?
        if( !in_array( $controllerName, $this->whitelist) )
            // nope
            return;

        // assign the status
        $view->assign( "atsdConfiguratorShopStatus", (boolean) $this->configuration[ "shopStatus" ] );

        // add template dir
        $view->addTemplateDir( $this->viewDir );

    }
}