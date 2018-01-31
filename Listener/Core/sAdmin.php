<?php

/**
 * Aquatuning Software Development - Configurator - Listener
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Listener\Core;

use Enlight_Hook_HookArgs as HookArgs;
use AtsdConfigurator\Components\Selection\BasketService;



/**
 * Aquatuning Software Development - Configurator - Listener
 */

class sAdmin
{

    /**
     * ...
     *
     * @var BasketService
     */

    protected $basketService;



    /**
     * ...
     *

     * @param BasketService   $basketService
     */

    public function __construct( BasketService $basketService )
    {
        // set params
        $this->basketService = $basketService;
    }



    /**
     * Add the weight to the dispatch basket to get correct shipping costs.
     *
     * @param HookArgs   $arguments
     *
     * @return array
     */

    public function afterGetDispatchBasket( HookArgs $arguments )
    {
        // get the query
        $basket = $arguments->getReturn();

        // no basket given?
        if ( ( !is_array( $basket ) ) or ( !isset( $basket['weight'] ) ) )
            // return default
            return $basket;

        // add the weight
        $basket['weight'] = (float) $basket['weight'] + $this->basketService->getBasketWeight( $basket['sessionID'] );

        // return the correct basket
        return $basket;
    }

}
