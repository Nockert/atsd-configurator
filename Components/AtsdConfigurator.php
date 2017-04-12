<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components;

use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use Enlight_Components_Session_Namespace as Session;
use Shopware\CustomModels\AtsdConfigurator\Configurator;
use Shopware\CustomModels\AtsdConfigurator\Selection;
use Shopware\AtsdConfigurator\Components;
use Shopware\CustomModels\AtsdConfigurator\Repository;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class AtsdConfigurator
{

    /**
     * DI container.
     *
     * @var Container
     */

    protected $container;



    /**
     * Shopware model manager.
     *
     * @var ModelManager
     */

    protected $modelManager;



    /**
     * Status of the internal cache
     *
     * @var boolean
     */

    private $cache = true;



    /**
     * Cache time for the internal cache.
     *
     * @var integer
     */

    private $cacheTime = 900;





    /**
     * ...
     *
     * @param Container      $container
     * @param ModelManager   $modelManager
     */

    public function __construct( Container $container, ModelManager $modelManager )
    {
        // set params
        $this->container    = $container;
        $this->modelManager = $modelManager;
    }







    /**
     * Returns the current session.
     *
     * @return Session
     */

    protected function getSession()
    {
        // return it
        return $this->container->get( "session" );
    }







    /**
     * Get the main repository.
     *
     * @return Repository
     */

    public function getRepository()
    {
        // return the default repository
        /* @var $repo Repository */
        $repo = $this->modelManager
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator' );

        // return it
        return $repo;
    }











    /**
     * Get the default configurator with default prices and stock information for the list
     * product service for all listing.
     *
     * @param integer   $configuratorId
     * @param boolean   $useCache
     *
     * @return array
     */

    public function getConfiguratorDefaults( $configuratorId, $useCache = true )
    {
        // get the cache
        $cache = $this->getSession()->offsetGet( "atsdConfiguratorDefaults" );

        // is this an array?
        if ( !is_array( $cache ) )
            // we need one
            $cache = array();

        // not cached
        if ( ( $useCache == false ) or ( $this->cache == false ) or ( !isset( $cache[$configuratorId] ) ) or ( $cache[$configuratorId]['time'] < time() - $this->cacheTime ) )
        {
            /* @var $defaultService Components\Selection\DefaultService */
            $defaultService = $this->container->get( "atsd_configurator.selection.default-service");

            /* @var $calculatorService Components\Selection\CalculatorService */
            $calculatorService = $this->container->get( "atsd_configurator.selection.calculator-service");

            // get default selection
            $selection = $defaultService->getDefaultSelection( $configuratorId );

            // parse it
            $parsed = $calculatorService->calculateSelectionData( $configuratorId, $selection, null, false, false );

            // new format
            $data = array(
                'price'          => $parsed['price'],
                'pseudoPrice'    => $parsed['pseudoPrice'],
                'hasPseudoPrice' => $parsed['hasPseudoPrice'],
                'stock'          => $parsed['stock']
            );

            // save it
            $cache[$configuratorId] = array(
                'time'  => time(),
                'cache' => $data
            );

            // set it back
            $this->getSession()->offsetSet( "atsdConfiguratorDefaults", $cache );
        }

        // return by cache
        return $cache[$configuratorId]['cache'];
    }








    /**
     * Get all relevant data for a selection in the checkout.
     *
     * @param Selection   $selection
     * @param boolean     $useCache
     *
     * @return array
     */

    public function getSelectionData( $selection, $useCache = true )
    {
        // not a valid selection?!
        if ( !$selection instanceof Selection )
            // return stuff
            return array(
                'valid' => false
            );

        // get the cache
        $cache = $this->getSession()->offsetGet( "atsdConfiguratorSelectionData" );

        // is this an array?
        if ( !is_array( $cache ) )
            // we need one
            $cache = array();

        // not cached
        if ( ( $useCache == false ) or ( $this->cache == false ) or ( !isset( $cache[$selection->getKey()] ) ) or ( $cache[$selection->getKey()]['time'] < time() - $this->cacheTime ) )
        {
            /* @var $calculatorService Components\Selection\CalculatorService */
            $calculatorService = $this->container->get( "atsd_configurator.selection.calculator-service");

            // get the data
            $data = $calculatorService->calculateSelectionDataBySelection( $selection );

            // save it
            $cache[$selection->getKey()] = array(
                'time'  => time(),
                'cache' => $data
            );

            // set it back
            $this->getSession()->offsetSet( "atsdConfiguratorSelectionData", $cache );
        }

        // return by cache
        return $cache[$selection->getKey()]['cache'];
    }






}



