<?php

/**
 * Aquatuning Software Development - Concerto - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConcerto
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber;



/**
 * Aquatuning Software Development - Concerto - Subscriber
 */

class Controllers implements \Enlight\Event\SubscriberInterface
{

	/**
	 * Main bootstrap object.
	 *
	 * @var \Shopware_Components_Plugin_Bootstrap
	 */

	protected $bootstrap;





	/**
	 * ...
	 *
	 * @param \Shopware_Components_Plugin_Bootstrap   $bootstrap
	 *
	 * @return \Shopware\AtsdConfigurator\Subscriber\Controllers
	 */

	public function __construct( \Shopware_Components_Plugin_Bootstrap $bootstrap )
	{
		// set params
		$this->bootstrap = $bootstrap;
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
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_AtsdConfigurator' => "onGetFrontendController",
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_AtsdConfigurator'  => 'onGetBackendController'
		);
	}






    /**
     * Returns the path to the frontend controller.
     *
     * @return string
     */

    public function onGetFrontendController()
    {
        // add template dir
        Shopware()->Template()->addTemplateDir(
            $this->bootstrap->Path() . "Views/"
        );

        // return the frontend controller path
        return $this->bootstrap->Path(). 'Controllers/Frontend/AtsdConfigurator.php';
    }





    /**
     * Get our backend controller.
     *
     * @return string
     */

    public function onGetBackendController()
    {
        // add template dir
        Shopware()->Template()->addTemplateDir(
            $this->bootstrap->Path() . "Views/"
        );

        // return our backend controller path
        return $this->bootstrap->Path(). "Controllers/Backend/AtsdConfigurator.php";
    }





}