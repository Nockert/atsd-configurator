<?php

/**
 * Aquatuning Software Development - Configurator - Listener
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Listener\Plugins\Frontend;

use Enlight_Event_EventArgs as EventArgs;
use Enlight_Components_Session_Namespace as Session;
use AtsdConfigurator\Components\Selection\BasketService;



/**
 * Aquatuning Software Development - Configurator - Listener
 */

class AtsdShippingUpsXml
{

	/**
	 * ...
	 *
	 * @var Session
	 */

	protected $session;



    /**
     * ...
     *
     * @var BasketService
     */

    protected $basketService;



    /**
	 * ...
	 *
	 * @param Session         $session
     * @param BasketService   $basketService
	 */

	public function __construct( Session $session, BasketService $basketService )
	{
		// set params
		$this->session       = $session;
		$this->basketService = $basketService;
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
        // get the weight
        $weight = $arguments->getReturn();

        // add our weight
        $weight = (float) $weight + $this->basketService->getBasketWeight( $this->session->get( "sessionId" ) );

        // return the correct weight
        return $weight;
    }

}
