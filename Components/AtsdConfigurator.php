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
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\MediaBundle\MediaService;



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
     * @return \Enlight_Config
     */

    protected function getConfig()
    {
        // return it
        return $this->bootstrap->Config();
    }






    /**
     * Returns the current session.
     *
     * @return \Enlight_Components_Session_Namespace
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
        $saleType = (integer) $this->bootstrap->Config()->get( "saleType" );

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
        // loop the fieldsets
        foreach ( $configurator['fieldsets'] as $fieldset )
        {
            // loop the elements
            foreach ( $fieldset['elements'] as $element )
            {
                // is this not mandatory?
                if ( $element['mandatory'] == false )
                    // no need to check
                    continue;

                // loop the articles
                foreach ( $element['articles'] as $article )
                {
                    // is this article active?
                    if ( ( $article['article']['active'] == false ) or ( $article['article']['mainDetail']['active'] == false ) )
                        // check next article
                        continue;

                    // not enough stock?
                    $maxStock = $this->getMaxArticleStock( $article['article']['mainDetail']['inStock'], $article['article']['lastStock'], $article['quantity'] );

                    // do we have this article to sell?
                    if ( $maxStock < 1 )
                        // we cant sell this one
                        continue;

                    // all good -> next element
                    continue 2;
                }

                // we reach here if no article is correct for this element
                return false;
            }
        }

        // every mandatory element has at least 1 element to sell
        return true;
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
        // get category config
        $allowArticlesWithoutCategory = (boolean) $this->bootstrap->Config()->get( "allowArticlesWithoutCategory" );

        // loop the fieldsets
        foreach ( $configurator['fieldsets'] as $fieldsetKey => $fieldset )
        {
            // loop the elements
            foreach ( $fieldset['elements'] as $elementKey => $element )
            {
                // loop the articles
                foreach ( $element['articles'] as $articleKey => $article )
                {
                    // can we use this article?
                    if ( ( $article['article']['active'] == false ) or ( $article['article']['mainDetail']['active'] == false ) or ( $this->getMaxArticleStock( $article['article']['mainDetail']['inStock'], $article['article']['lastStock'], $article['quantity'] ) < 1 ) )
                        // no we cant
                        unset( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey]['articles'][$articleKey] );

                    // do not allow articles without category
                    if ( $allowArticlesWithoutCategory == false )
                    {
                        // get one category
                        $categoryId = (integer) Shopware()->Modules()->Categories()->sGetCategoryIdByArticleId( $article['article']['id'] );

                        // do we have none?!
                        if ( $categoryId == 0 )
                            // remove the article
                            unset( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey]['articles'][$articleKey] );
                    }
                }

                // no articles left for this element?
                if ( count( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey]['articles'] ) == 0 )
                    // remove the element
                    unset( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey] );
            }

            // are there any elements left?!
            if ( count( $configurator['fieldsets'][$fieldsetKey]['elements'] ) == 0 )
                // remove the fieldset
                unset( $configurator['fieldsets'][$fieldsetKey] );
        }

        // return the clean configurator
        return $configurator;
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
        // get the articles
        $articleIds     = array();
        $articleNumbers = array();



        // add current main article
        if ( $includeMaster == true )
        {
            // do it
            array_push( $articleIds, $configurator['article']['id'] );
            array_push( $articleNumbers, $configurator['article']['mainDetail']['number'] );
        }



        // loop the configurator to get every article id and number
        foreach ( $configurator['fieldsets'] as $fieldset )
        {
            // loop the elements
            foreach ( $fieldset['elements'] as $element )
            {
                // loop the articles
                foreach ( $element['articles'] as $article )
                {
                    // add it
                    array_push( $articleIds, $article['article']['id'] );
                    array_push( $articleNumbers, $article['article']['mainDetail']['number'] );
                }
            }
        }

        // unique it
        $articleIds     = array_unique( $articleIds );
        $articleNumbers = array_unique( $articleNumbers );



        // get the service
        /* @var $listProductService ListProductServiceInterface */
        $listProductService = $this->container->get('shopware_storefront.list_product_service');

        // get all products
        $products = $listProductService->getList(
            $articleNumbers,
            $this->contextService->getProductContext()
        );



        // save the products back to the configurator
        foreach ( $configurator['fieldsets'] as $fieldsetKey => $fieldset )
        {
            // loop the elements
            foreach ( $fieldset['elements'] as $elementKey => $element )
            {
                // loop the articles
                foreach ( $element['articles'] as $articleKey => $article )
                {
                    // get the product
                    $product = $products[$article['article']['mainDetail']['number']];

                    // we have to reset the cover thumbnail for shopware 5.1
                    if ( ( $this->bootstrap->isShopware51() == true ) and ( $product->getCover() instanceof Struct\Media ) )
                    {
                        // do we even have a single thumbnail?
                        if ( count( $product->getCover()->getThumbnails() ) > 0 )
                        {
                            // due to a bug before 5.1.2 we have to create a new thumbnail with the media service
                            $thumbnail = new Struct\Thumbnail(
                                $this->mediaService->getUrl( $product->getCover()->getThumbnail( 0 )->getSource() ),
                                $this->mediaService->getUrl( $product->getCover()->getThumbnail( 0 )->getRetinaSource() ),
                                $product->getCover()->getThumbnail( 0 )->getMaxWidth(),
                                $product->getCover()->getThumbnail( 0 )->getMaxHeight()
                            );
                        }
                        // we dont have a thumbnail
                        else
                        {
                            // use the default cover image
                            $thumbnail = new Struct\Thumbnail(
                                $product->getCover()->getFile(),
                                $product->getCover()->getFile(),
                                $product->getCover()->getWidth(),
                                $product->getCover()->getHeight()
                            );
                        }

                        // and set it
                        $product->getCover()->setThumbnails( array( $thumbnail ) );
                    }

                    // save it
                    $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey]['articles'][$articleKey]['article'] = $product;
                }
            }
        }



        // and set the main article
        if ( $includeMaster == true )
            // do it
            $configurator['article'] = $products[$configurator['article']['mainDetail']['number']];

        // return it
        return $configurator;
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
        // save the products back to the configurator
        foreach ( $configurator['fieldsets'] as $fieldset )
        {
            // loop the elements
            foreach ( $fieldset['elements'] as $element )
            {
                // is this optional?
                if ( $element['mandatory'] == false )
                    // ignore it
                    continue;

                // loop the articles
                foreach ( $element['articles'] as $article )
                {
                    // did we select this article?
                    if ( in_array( $article['id'], $selection ) )
                        // all good -> next element
                        continue 2;
                }

                // none of these mandatory articles are selected
                return false;
            }
        }

        // all good
        return true;
    }






    /**
     * Get a default selection for a specified configurator. We always use the first article
     * of every mandatory element.
     *
     * @param integer   $configuratorId
     *
     * @return array
     */

    public function getDefaultSelection( $configuratorId )
    {
        // get builder to get the full configurator again
        $builder = $this->getRepository()
            ->getPartialConfiguratorWithArticlesQueryBuilder();

        // just the one
        $builder->andWhere( "configurator.id = :configuratorId" )
            ->setParameter( "configuratorId", $configuratorId );

        // get all
        $configurators = $builder->getQuery()->getArrayResult();

        // the first
        $configurator = $configurators[0];



        // our selection
        $selection = array();

        // save the products back to the configurator
        foreach ( $configurator['fieldsets'] as $fieldset )
        {
            // loop the elements
            foreach ( $fieldset['elements'] as $element )
            {
                // is this optional?
                if ( $element['mandatory'] == false )
                    // ignore it
                    continue;

                // just set the first article of this element
                array_push(
                    $selection,
                    (integer) $element['articles'][0]['id']
                );
            }
        }

        // return the selection
        return $selection;
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
     * @param \Shopware\CustomModels\AtsdConfigurator\Selection   $selection
     * @param boolean                                             $useCache
     *
     * @return array
     */

    public function getSelectionData( $selection, $useCache = true )
    {
        // not a valid selection?!
        if ( !$selection instanceof \Shopware\CustomModels\AtsdConfigurator\Selection )
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
     * @param \Shopware\CustomModels\AtsdConfigurator\Selection   $selection
     *
     * @return array
     */

    private function calculateSelectionDataBySelection( \Shopware\CustomModels\AtsdConfigurator\Selection $selection )
    {
        // get the selected article ids
        $selectionArr = array_map(
            function( \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article $article ) {
                return $article->getId();
            },
            $selection->getArticles()->toArray()
        );

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
     * @return array
     */

    protected function calculateSelectionData( $configuratorId, array $selection, $key = null, $validate = true, $includeMaster = true )
    {
        // default return value
        $return = array(
            'valid'          => false,
            'key'            => "",
            'hasPseudoPrice' => false,
            'pseudoPrice'    => 0.0,
            'price'          => 0.0,
            'pseudoPriceNet' => 0.0,
            'priceNet'       => 0.0,
            'stock'          => 0,
            'weight'         => 0,
            'article'        => array(),
            'fieldsets'      => array()
        );



        // get the configurator
        $configurator = $this->getParsedConfiguratorForSelection( $configuratorId, $selection, $validate, $includeMaster );



        // do want to include the master article?
        if ( $includeMaster == true )
        {
            // get main article
            /* @var $article Struct\ListProduct */
            $article = $configurator['article'];

            // set article data
            $return['article'] = array(
                'id'       => $article->getId(),
                'number'   => $article->getNumber(),
                'name'     => $article->getName(),
                'quantity' => 1,
                'price'    => ( $configurator['chargeArticle'] == true ) ? $this->getArticlePrice( $article, 1 ) * ( ( 100 - (integer) $configurator['rebate'] ) / 100 ) : 0.0,
                'priceNet' => ( $configurator['chargeArticle'] == true ) ? $this->getArticleNetPrice( $article, 1 ) * ( ( 100 - (integer) $configurator['rebate'] ) / 100 ) : 0.0
            );
        }



        // set default max stock
        $return['stock'] = 100;



        // now loop everything again to finally calculate the data
        foreach ( $configurator['fieldsets'] as $fieldsetKey => $fieldset )
        {
            // current fieldset return
            $returnFieldset = array(
                'description' => $fieldset['description'],
                'elements'    => array()
            );

            // loop the elements
            foreach ( $fieldset['elements'] as $elementKey => $element )
            {
                // current return
                $returnElement = array(
                    'description' => $element['description'],
                    'articles'    => array()
                );

                // loop the articles
                foreach ( $element['articles'] as $articleKey => $article )
                {
                    // some vars
                    $quantity = (integer) $article['quantity'];
                    $rebate   = (integer) $configurator['rebate'];

                    // article struct
                    /* @var $articleStruct Struct\ListProduct */
                    $articleStruct = $article['article'];

                    // add weight
                    $return['weight'] += $quantity * $articleStruct->getWeight();

                    // set stock
                    $return['stock'] = min( $return['stock'], floor( $articleStruct->getStock() / $quantity ) );

                    // price calculations
                    $price    = $this->getArticlePrice( $articleStruct, $quantity );
                    $priceNet = $this->getArticleNetPrice( $articleStruct, $quantity );

                    // set prices
                    $return['pseudoPrice'] += $price;
                    $return['price']       += $price * ( ( 100 - $rebate ) / 100 );

                    // set net prices
                    $return['pseudoPriceNet'] += $priceNet;
                    $return['priceNet']       += $priceNet * ( ( 100 - $rebate ) / 100 );

                    // save article
                    array_push(
                        $returnElement['articles'],
                        array(
                            'id'       => $articleStruct->getId(),
                            'number'   => $articleStruct->getNumber(),
                            'name'     => $articleStruct->getName(),
                            'quantity' => $quantity,
                            'price'    => $price * ( ( 100 - $rebate ) / 100 ),
                            'priceNet' => $priceNet * ( ( 100 - $rebate ) / 100 )
                        )
                    );
                }

                // save it
                array_push(
                    $returnFieldset['elements'],
                    $returnElement
                );
            }

            // add it
            array_push(
                $return['fieldsets'],
                $returnFieldset
            );
        }



        // do we want to include the master?
        if ( $includeMaster == true )
        {
            // main article
            /* @var $article Struct\ListProduct */
            $article = $configurator['article'];

            // for the main article
            $return['weight'] += $article->getWeight();

            // price calculations
            $price    = ( $configurator['chargeArticle'] == true ) ? $this->getArticlePrice( $article, 1 ) : 0.0;
            $priceNet = ( $configurator['chargeArticle'] == true ) ? $this->getArticleNetPrice( $article, 1 ) : 0.0;

            // set prices
            $return['pseudoPrice'] += $price;
            $return['price']       += $price;

            // set net prices
            $return['pseudoPriceNet'] += $priceNet;
            $return['priceNet']       += $priceNet;
        }



        // all good
        $return['valid'] = true;

        // selector key
        $return['key'] = $key;

        // prices
        $return['hasPseudoPrice'] = ( $return['price'] != $return['pseudoPrice'] );

        // return it
        return $return;
    }






    /**
     * ...
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Selection   $selection
     * @param boolean                                             $validate
     *
     * @return array|null
     */

    public function getParsedConfiguratorForSelectionBySelection( \Shopware\CustomModels\AtsdConfigurator\Selection $selection, $validate = true )
    {
        // get the selected article ids
        $selectionArr = array_map(
            function( \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article $article ) {
                return $article->getId();
            },
            $selection->getArticles()->toArray()
        );

        // call method
        return $this->getParsedConfiguratorForSelection( $selection->getConfigurator()->getId(), $selectionArr, $validate );
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
        // get builder to get the full configurator again
        $builder = $this->getRepository()
            ->getPartialConfiguratorWithArticlesQueryBuilder();

        // just the one
        $builder->andWhere( "configurator.id = :configuratorId" )
            ->setParameter( "configuratorId", $configuratorId );

        // get all
        $configurators = $builder->getQuery()->getArrayResult();

        // the first
        $configurator = $configurators[0];



        // remove invalid stuff
        $configurator = $this->filterConfigurator( $configurator );

        // do we want to validate?
        if ( ( $validate == true ) and ( $this->validateSelection( $configurator, $selection ) == false ) )
            // invalid
            return null;



        // we only want selected articles
        foreach ( $configurator['fieldsets'] as $fieldsetKey => $fieldset )
        {
            // loop the elements
            foreach ( $fieldset['elements'] as $elementKey => $element )
            {
                // loop the articles
                foreach ( $element['articles'] as $articleKey => $article )
                {
                    // is this article not selected?
                    if ( !in_array( $article['id'], $selection ) )
                        // remove it
                        unset( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey]['articles'][$articleKey] );
                }
            }
        }



        // parse it now
        $configurator = $this->parseConfigurator( $configurator, $includeMaster );

        // return it
        return $configurator;
    }






    /**
     * Get a default selection for a specified configurator. We always use the first article
     * of every mandatory element.
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Configurator   $configurator
     * @param array                                                  $articleIds
     * @param boolean                                                $manual
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Selection
     */

    public function createSelection( \Shopware\CustomModels\AtsdConfigurator\Configurator $configurator, array $articleIds, $manual = false )
    {
        // create a new selection
        $selection = new \Shopware\CustomModels\AtsdConfigurator\Selection();

        // set default values
        $selection->setConfigurator( $configurator );
        $selection->setCustomer( $this->getCustomer() );
        $selection->setManual( $manual );

        // loop the articleids
        foreach ( $articleIds as $articleId )
        {
            // get the article
            /* @var $article \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article */
            $article = $this->modelManager
                ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article' )
                ->findOneBy( array( 'id' => (integer) $articleId ) );

            // not found?
            if ( !$article instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article )
                // next
                continue;

            // add it to the selection
            $selection->addArticle( $article );
        }

        // save it
        $this->modelManager->persist( $selection );
        $this->modelManager->flush( $selection );

        // and return it
        return $selection;
    }






    /**
     * ...
     *
     * @return \Shopware\Models\Customer\Customer
     */

    public function getCustomer()
    {
        // get the customer id (if logged in)
        $customerId = (integer) $this->getSession()->offsetGet( "sUserId" );

        // get the customer
        /* @var $customer \Shopware\Models\Customer\Customer */
        $customer = $this->modelManager
            ->getRepository( '\Shopware\Models\Customer\Customer' )
            ->find( $customerId );

        // return it
        return $customer;
    }







    /**
     *
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Selection   $selection
     *
     * @return void
     */

    public function addSelectionToBasket( \Shopware\CustomModels\AtsdConfigurator\Selection $selection )
    {
        // get the article
        $article = $selection->getConfigurator()->getArticle();

        // get detail
        $detail = $article->getMainDetail();

        // currency
        $currency = $this->getCurrency();



        // create the basket item
        $item = new \Shopware\Models\Order\Basket();

        // persist
        $this->modelManager->persist( $item );

        // set a few parameters
        $item->setSessionId( $this->getSession()->offsetGet( "sessionId" ) );
        $item->setCustomerId( (integer) $this->getSession()->offsetGet( "sUserId" ) );
        $item->setArticleName( $article->getName() );
        $item->setArticleId( $article->getId() );
        $item->setOrderNumber( $detail->getNumber() );
        $item->setShippingFree( $detail->getShippingFree() );
        $item->setQuantity( 1 );
        $item->setDate( new \DateTime() );
        $item->setMode( 0 );
        $item->setEsdArticle( 0 );
        $item->setPartnerId( (string) $this->getSession()->offsetGet( "sPartner" ) );
        $item->setLastViewPort( "" );
        $item->setUserAgent( "" );
        $item->setConfig( "" );

        // price will be reset anyway
        $item->setPrice( 0 );
        $item->setNetPrice( 0 );

        // more price info
        $item->setTaxRate( $this->getTaxRate( $article->getTax()->getId() ) );
        $item->setCurrencyFactor( $currency->getFactor() );



        // create the attribute
        $attribute = new \Shopware\Models\Attribute\OrderBasket();

        // persist
        $this->modelManager->persist( $attribute );

        // set bundle data
        $attribute->setAtsdConfiguratorSelectionId( $selection->getId() );

        // add the attribute to the item
        $item->setAttribute( $attribute );



        // save shit
        $this->modelManager->flush();
    }






    /**
     * ...
     *
     * @param Struct\ListProduct   $article
     * @param integer              $quantity
     *
     * @return Struct\Product\Price
     */

    public function getArticlePriceStructForQuantity( Struct\ListProduct $article, $quantity )
    {
        // loop every available price
        foreach ( $article->getPrices() as $price )
        {
            // get the rule
            $rule = $price->getRule();

            // final price?
            if ( $rule->getTo() === null )
                // we found it
                return $price;

            // found it?
            if ( ( $quantity >= $price->getFrom() ) and ( $quantity <= $price->getTo() ) )
                // yep
                return $price;
        }

        // none found?! get the cheapest
        return $article->getCheapestPrice();
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
        // get the price struct
        $price = $this->getArticlePriceStructForQuantity( $article, $quantity );

        // calculate final price
        return $price->getCalculatedPrice() * $quantity;
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
        // get the price struct
        $price = $this->getArticlePriceStructForQuantity( $article, $quantity );

        // calculate final price
        return $price->getRule()->getPrice() * $quantity;
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
        // call the sarticles module
        return (float) Shopware()->Modules()->Articles()->getTaxRateByConditions( $id );
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
        // return via context service
        return Shopware()->Modules()->Articles()->sFormatPrice( $price );
    }





}



