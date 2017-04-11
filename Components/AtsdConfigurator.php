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
use Shopware_Plugins_Frontend_AtsdConfigurator_Bootstrap as Bootstrap;
use Shopware\Components\Model\ModelManager;
use Enlight_Components_Session_Namespace as Session;
use Enlight_Config as Config;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\MediaBundle\MediaService;
use Shopware\CustomModels\AtsdConfigurator\Configurator;
use Shopware\CustomModels\AtsdConfigurator\Selection;
use Shopware\AtsdConfigurator\Components;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class AtsdConfigurator
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
     * Shopware context service.
     *
     * @var ContextService
     */

    protected $contextService;



    /**
     * Shopware context service.
     *
     * @var MediaService
     */

    protected $mediaService;



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
     * @param Bootstrap   $bootstrap
     * @param Container   $container
     *
     * @return AtsdConfigurator
     */

    public function __construct( Bootstrap $bootstrap, Container $container )
    {
        // set params
        $this->bootstrap = $bootstrap;
        $this->container = $container;

        // set internal params
        $this->contextService = $this->container->get( "shopware_storefront.context_service" );
        $this->mediaService   = $this->container->get( "shopware_media.media_service" );
        $this->modelManager   = $this->container->get( "shopware.model_manager" );
    }






    /**
     * Returns the config.
     *
     * @return Config
     */

    protected function getConfig()
    {
        // return it
        return $this->bootstrap->Config();
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
     * @return \Shopware\CustomModels\AtsdConfigurator\Repository
     */

    public function getRepository()
    {
        // return the default repository
        return $this->modelManager
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator' );
    }










    /**
     * Validate a configurator and check if its even possible to configure it or if mandatory articles
     * arent available.
     *
     * @param array   $configurator
     *
     * @return boolean
     */

    public function valdiateConfigurator( array $configurator )
    {
        /* @var $validatorService Components\Configurator\ValidatorService */
        $validatorService = $this->container->get( "atsd_configurator.configurator.validator-service");

        // ...
        return $validatorService->valdiate( $configurator );
    }








    /**
     * Filters the configurator and removes invalid articles and empty elements.
     *
     * @param array   $configurator
     *
     * @return array
     */

    public function filterConfigurator( array $configurator )
    {
        /* @var $filterService Components\Configurator\FilterService */
        $filterService = $this->container->get( "atsd_configurator.configurator.filter-service");

        // ...
        return $filterService->filter( $configurator );
    }










    /**
     * Parse the configurator and replace the articles with the listProduct structure to get
     * every information like price, image etc.
     *
     * @param array     $configurator
     * @param boolean   $includeMaster
     *
     * @return array
     */

    public function parseConfigurator( array $configurator, $includeMaster = true )
    {
        /* @var $parserService Components\Configurator\ParserService */
        $parserService = $this->container->get( "atsd_configurator.configurator.parser-service");

        // ...
        return $parserService->parse( $configurator, $includeMaster );
    }






    /**
     * Checks if the selection is complete for the configurator.
     *
     * @param array   $configurator
     * @param array   $selection
     *
     * @return boolean
     */

    public function validateSelection( array $configurator, array $selection )
    {
        /* @var $validatorService Components\Selection\ValidatorService */
        $validatorService = $this->container->get( "atsd_configurator.selection.validator-service");

        // ...
        return $validatorService->validate( $configurator, $selection );
    }










    /**
     * Get a default selection for a specified configurator. We always use the first article
     * of every mandatory element.
     *
     * The returning selections key = element article id and value = quantity.
     *
     * Example:
     * array(
     *     1 => 15
     *     2 => 5
     * );
     *
     * @param integer   $configuratorId
     *
     * @return array
     */

    public function getDefaultSelection( $configuratorId )
    {
        /* @var $defaultService Components\Selection\DefaultService */
        $defaultService = $this->container->get( "atsd_configurator.selection.default-service");

        // ...
        return $defaultService->getDefaultSelection( $configuratorId );
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
            // get default selection
            $selection = $this->getDefaultSelection( $configuratorId );

            // parse it
            $parsed = $this->calculateSelectionData( $configuratorId, $selection, null, false, false );

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
            // get the data
            $data = $this->calculateSelectionDataBySelection( $selection );

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





    /**
     * ...
     *
     * @param Selection   $selection
     *
     * @return array
     */

    private function calculateSelectionDataBySelection( Selection $selection )
    {
        // the selection array
        $selectionArr = array();

        // loop all articles
        /* @var $article Selection\Article */
        foreach ( $selection->getArticles() as $article )
            // add it
            $selectionArr[$article->getArticle()->getId()] = $article->getQuantity();

        // call by array
        return $this->calculateSelectionData( $selection->getConfigurator()->getId(), $selectionArr, $selection->getKey() );
    }




    /**
     * ...
     *
     * @param integer   $configuratorId
     * @param array     $selection
     * @param string    $key
     * @param boolean   $validate
     * @param boolean   $includeMaster
     *
     * @throws Exception\ValidatorException
     *
     * @return array
     */

    protected function calculateSelectionData( $configuratorId, array $selection, $key = null, $validate = true, $includeMaster = true )
    {
        /* @var $calculatorService Components\Selection\CalculatorService */
        $calculatorService = $this->container->get( "atsd_configurator.selection.calculator-service");

        // ...
        return $calculatorService->calculateSelectionData( $configuratorId, $selection, $key, $validate, $includeMaster );
    }






    /**
     * ...
     *
     * @param Selection   $selection
     * @param boolean     $validate
     *
     * @return array|null
     */

    public function getParsedConfiguratorForSelectionBySelection( Selection $selection, $validate = true )
    {
        /* @var $parserService Components\Selection\ParserService */
        $parserService = $this->container->get( "atsd_configurator.selection.parser-service");

        // ...
        return $parserService->getParsedConfiguratorForSelectionBySelection( $selection, $validate );
    }







    /**
     * Get a configurator with all article information - just for the selection.
     * Returns the configurator or null if validation fails.
     *
     * @param integer   $configuratorId
     * @param array     $selection
     * @param boolean   $validate
     * @param boolean   $includeMaster
     *
     * @return array|null
     */

    public function getParsedConfiguratorForSelection( $configuratorId, array $selection, $validate = true, $includeMaster = true )
    {
        /* @var $parserService Components\Selection\ParserService */
        $parserService = $this->container->get( "atsd_configurator.selection.parser-service");

        // ...
        return $parserService->getParsedConfiguratorForSelection( $configuratorId, $selection, $validate, $includeMaster );
    }






    /**
     * Get a default selection for a specified configurator. We always use the first article
     * of every mandatory element.
     *
     * @param Configurator   $configurator
     * @param array          $articles
     * @param boolean        $manual
     *
     * @return Selection
     */

    public function createSelection( Configurator $configurator, array $articles, $manual = false )
    {
        /* @var $creatorService Components\Selection\CreatorService */
        $creatorService = $this->container->get( "atsd_configurator.selection.creator-service");

        // ...
        return $creatorService->createSelection( $configurator, $articles, $manual );
    }





    /**
     *
     *
     * @param Selection   $selection
     *
     * @return void
     */

    public function addSelectionToBasket( Selection $selection )
    {
        /* @var $basketService Components\Selection\BasketService */
        $basketService = $this->container->get( "atsd_configurator.selection.basket-service");

        // ...
        $basketService->addSelectionToBasket( $selection );
    }







    /**
     * ...
     *
     * @param Struct\ListProduct   $article
     *
     * @return float
     */

    public function getArticlePrice( Struct\ListProduct $article, $quantity )
    {
        /* @var $priceService Components\Configurator\ArticlePriceService */
        $priceService = $this->container->get( "atsd_configurator.configurator.article-price-service");

        // ...
        return $priceService->getArticlePrice( $article, $quantity );
    }






    /**
     * ...
     *
     * @param Struct\ListProduct   $article
     * @param integer              $quantity
     *
     * @return float
     */

    public function getArticleNetPrice( Struct\ListProduct $article, $quantity )
    {
        /* @var $priceService Components\Configurator\ArticlePriceService */
        $priceService = $this->container->get( "atsd_configurator.configurator.article-price-service");

        // ...
        return $priceService->getArticleNetPrice( $article, $quantity );
    }





    /**
     * Get the tax rate for a tax id.
     *
     * @param integer   $id
     *
     * @return float
     */

    public function getTaxRate( $id )
    {
        /* @var $priceService Components\Configurator\ArticlePriceService */
        $priceService = $this->container->get( "atsd_configurator.configurator.article-price-service");

        // ...
        return $priceService->getTaxRate( $id );
    }






    /**
     * Returns the current currency.
     *
     * @return Struct\Currency
     */

    public function getCurrency()
    {
        // return via context service
        return $this->contextService->getShopContext()->getCurrency();
    }






    /**
     * ...
     *
     * @param float   $price
     *
     * @return string
     */

    public function formatPrice( $price )
    {
        /* @var $priceService Components\Configurator\ArticlePriceService */
        $priceService = $this->container->get( "atsd_configurator.configurator.article-price-service");

        // ...
        return $priceService->formatPrice( $price );
    }





}



