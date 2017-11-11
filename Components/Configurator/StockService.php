<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Components\Configurator;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class StockService
{

    /**
     * ...
     *
     * @var array
     */

    protected $configuration;



    /**
     * ...
     *
     * @param array   $configuration
     */

    public function __construct( array $configuration )
    {
        // set params
        $this->configuration = $configuration;
    }



    /**
     * Get the max sellable stock for a single article for a specified configurator quantity
     * depending on our configuration.
     *
     * @param integer   $stock       the article stock
     * @param boolean   $lastStock   the last stock flag of the article
     * @param integer   $quantity    the requested quantity
     * @param integer   $max         the maximum quantity
     *
     * @return integer
     */

    public function getMaxArticleStock( $stock, $lastStock, $quantity, $max = 100 )
    {
        // get the sales type from the configuration
        $saleType = (integer) $this->configuration[ "saleType" ];

        // always for sale?
        if ( $saleType == 0 )
            // all good
            return $max;

        // dont sell eol?!
        if ( $saleType == 1 )
            // is this eol?
            return ( $lastStock == true )
                ? floor( $stock / $quantity )
                : $max;

        // never oversell
        return floor( $stock / $quantity );
    }

}
