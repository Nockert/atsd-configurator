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
use Enlight_Event_EventArgs as EventArgs;
use sOrder as CoreClass;
use AtsdConfigurator\Components\Selection\ParserService;
use AtsdConfigurator\Components\Selection\CalculatorService;
use AtsdConfigurator\Components\Configurator\ArticlePriceService;
use AtsdConfigurator\Models\Selection;
use Shopware\Components\Model\ModelManager;
use Enlight_Components_Db_Adapter_Pdo_Mysql as Db;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\AttributeBundle\Service\DataLoader as AttributeDataLoader;
use Shopware\Bundle\AttributeBundle\Service\DataPersister as AttributeDataPersister;



/**
 * Aquatuning Software Development - Configurator - Listener
 */

class sOrder
{

    /**
     * ...
     *
     * @var ModelManager
     */

    protected $modelManager;



    /**
     * ...
     *
     * @var Db
     */

    protected $db;



    /**
     * ...
     *
     * @var AttributeDataLoader
     */

    protected $attributeDataLoader;



    /**
     * ...
     *
     * @var AttributeDataPersister
     */

    protected $attributeDataPersister;



    /**
     * ...
     *
     * @var ParserService
     */

    private $parserService;



    /**
     * ...
     *
     * @var CalculatorService
     */

    private $calculatorService;



    /**
     * ...
     *
     * @var ArticlePriceService
     */

    private $articlePriceService;



    /**
     * ...
     *
     * @var array
     */

    private $configuration;



    /**
     * ...
     *
     * @param ModelManager             $modelManager
     * @param Db                       $db
     * @param AttributeDataLoader      $attributeDataLoader
     * @param AttributeDataPersister   $attributeDataPersister
     * @param ParserService            $parserService
     * @param CalculatorService        $calculatorService
     * @param ArticlePriceService      $articlePriceService
     * @param array                    $configuration
     */

    public function __construct( ModelManager $modelManager, Db $db, AttributeDataLoader $attributeDataLoader, AttributeDataPersister $attributeDataPersister, ParserService $parserService, CalculatorService $calculatorService, ArticlePriceService $articlePriceService, array $configuration )
    {
        // set params
        $this->modelManager           = $modelManager;
        $this->db                     = $db;
        $this->attributeDataLoader    = $attributeDataLoader;
        $this->attributeDataPersister = $attributeDataPersister;
        $this->parserService          = $parserService;
        $this->calculatorService      = $calculatorService;
        $this->articlePriceService    = $articlePriceService;
        $this->configuration          = $configuration;
    }



    /**
     * ...
     *
     * @param EventArgs   $arguments
     *
     * @return void
     */

    public function onProcessDetails( EventArgs $arguments )
    {
        // get the basket
        $basket = $arguments->get( "details" );

        // do we want to split?
        $split = (boolean) $this->configuration['splitConfigurator'];
        $attr  = (string)  $this->configuration['saveInAttribute'];



        // loop the basket
        foreach ( $basket as $article )
        {
            // do we have a selection?
            if ( (integer) $article['atsd_configurator_selection_id'] == 0 )
                // no
                continue;

            // update the attribute
            $query = "
                UPDATE s_order_details_attributes
                SET atsd_configurator_selection_id = '" . (integer) $article['atsd_configurator_selection_id'] . "',
                    atsd_configurator_selection_master = '" . ( ( (boolean) $article['atsd_configurator_selection_master'] == true ) ? "1" : "0" ) . "'
                WHERE detailID = " . (integer) $article['orderDetailId'] . "
            ";
            $this->db->exec( $query );



            // get attributes
            $attributes = $this->attributeDataLoader->load( "s_order_details_attributes", (integer) $article['orderDetailId'] );

            // reset our attribute
            $attributes[$attr] = $article['atsd_configurator_split_string'];

            // save it
            $this->attributeDataPersister->persist( $attributes, "s_order_details_attributes", (integer) $article['orderDetailId'] );
        }
    }



    /**
     * Split the configurator into single articles.
     *
     * @param HookArgs   $arguments
     *
     * @return void
     */

    public function beforeSaveOrder( HookArgs $arguments )
    {
        // get the controller
        /* @var $sOrder \sOrder */
        $sOrder = $arguments->getSubject();

        // split items and save attribute
        $this->parseOrderBasket( $sOrder );
    }



    /**
     * ...
     *
     * @param CoreClass   $sOrder
     *
     * @return void
     */

    private function parseOrderBasket( CoreClass $sOrder )
    {
        // do we want to split?
        $split = (boolean) $this->configuration[ "splitConfigurator" ];
        $attr  = (string)  $this->configuration[ "saveInAttribute" ];



        // our new return basket
        $return = array();

        // loop the current basket
        foreach ( $sOrder->sBasketData["content"] as $item )
        {
            // get the selection id
            $selectionId = (integer) $item['atsd_configurator_selection_id'];

            // do we have a selection?
            if ( $selectionId == 0 )
            {
                // add the default article
                array_push( $return, $item );

                // next
                continue;
            }



            // get the selector
            /* @var $selection Selection */
            $selection = $this->modelManager
                ->getRepository( Selection::class )
                ->find( $selectionId );

            // get basket data
            $configurator = $this->parserService->getParsedConfiguratorForSelectionBySelection( $selection, false );



            // get the calculated selection
            $calculatedSelection = $this->calculatorService->calculateSelectionDataBySelection( $selection );



            // the selection array
            $selectionArr = array();

            // loop all articles
            /* @var $article Selection\Article */
            foreach ( $selection->getArticles() as $article )
                // add it
                $selectionArr[$article->getArticle()->getId()] = $article->getQuantity();



            // do we want to split?!
            if ( $split == true )
            {
                // get the main article
                /* @var $article Struct\ListProduct */
                $article = $configurator['article'];

                // get the corrected item
                $item = $this->getBasketItem( $selection, $calculatedSelection['article'], $article, 1, 0, true, (integer) $item['id'] );
            }



            // our attribute to save the selection in it
            $attribute = array();

            // our split articles if we have any
            $splitArticles = array();



            // loop all articles - fieldsets first
            foreach ( $configurator['fieldsets'] as $fieldset )
            {
                // next elements
                foreach ( $fieldset['elements'] as $element )
                {
                    // article string for this element
                    $elementStr = $element['description'] . ": ";

                    // articles for this element
                    $elementArticles = array();



                    // next articles
                    foreach ( $element['articles'] as $articleArr )
                    {
                        // get the article
                        /* @var $article Struct\ListProduct */
                        $article = $articleArr['article'];

                        // get the selected quantity
                        $quantity = $selectionArr[$articleArr['id']];

                        // our article string
                        array_push(
                            $elementArticles,
                            $quantity . "x " . $article->getNumber() . " " . $article->getName()
                        );

                        // we do NOT want to split?
                        if ( $split == false )
                            // next one
                            continue;

                        // get the configurator article id
                        $configuratorArticleId = $articleArr['id'];

                        // ...
                        $selectionArticle = $this->getSelectionArticleByConfiguratorArticleId( $calculatedSelection, $configuratorArticleId );

                        // get the item
                        $splitArticleItem = $this->getBasketItem(
                            $selection,
                            $selectionArticle,
                            $article,
                            (integer) $quantity,
                            (integer) $configurator['rebate'],
                            false,
                            (integer) $item['id']
                        );

                        // add it
                        array_push( $splitArticles, $splitArticleItem );
                    }



                    // add this element to the attribute field
                    array_push( $attribute, $elementStr . implode( ", ", $elementArticles ) );
                }
            }



            // do we want to save the attribute?
            if ( $attr != "" )
            {
                // set for shopware 5.2 and higher
                $item[$attr] = implode( "\n", $attribute );
                $item['atsd_configurator_split_string'] = implode( "\n", $attribute );
            }



            // now add the master article
            array_push( $return, $item );

            // loop every split article
            foreach ( $splitArticles as $splitArticleItem )
                // add it
                array_push( $return, $splitArticleItem );
        }



        // set our new basket
        $sOrder->sBasketData['content'] = $return;
    }



    /**
     * ...
     *
     * @param array     $calculatedSelection
     * @param integer   $configuratorArticleId
     *
     * @return array
     */

    private function getSelectionArticleByConfiguratorArticleId( array $calculatedSelection, $configuratorArticleId )
    {
        // ...
        foreach ( $calculatedSelection['fieldsets'] as $fieldset )
        {
            // ...
            foreach ( $fieldset['elements'] as $element )
            {
                // ...
                foreach ( $element['articles'] as $article )
                {
                    // ...
                    if ( $article['configuratorArticleId'] == $configuratorArticleId )
                        // return it
                        return $article;
                }
            }
        }

        // ...
        return array();
    }



    /**
     * ...
     *
     * @param Selection            $selection
     * @param array                $selectionArticle
     * @param Struct\ListProduct   $article
     * @param integer              $quantity
     * @param integer              $rebate
     * @param boolean              $master
     * @param integer              $basketId
     *
     * @return array
     */

    private function getBasketItem( Selection $selection, array $selectionArticle, Struct\ListProduct $article, $quantity, $rebate, $master, $basketId )
    {
        // get price data
        $price    = ( ( $master == false ) or ( $master == true && $selection->getConfigurator()->getChargeArticle() == true ) ) ? $selectionArticle['price'] : 0.0;
        $priceNet = ( ( $master == false ) or ( $master == true && $selection->getConfigurator()->getChargeArticle() == true ) ) ? $selectionArticle['priceNet'] : 0.0;

        // default item
        $item = array(

            // default data
            'id'           => $basketId,
            'articleID'    => $article->getId(),
            'articlename'  => $article->getName(),
            'ordernumber'  => $article->getNumber(),
            'shippingfree' => $article->isShippingFree(),
            'quantity'     => $quantity,
            'modus'        => "0",
            'esdarticle'   => false,
            'tax_rate'     => $this->articlePriceService->getTaxRate( $article->getTax()->getTax() ),
            'taxID'        => $article->getTax()->getId(),
            'instock'      => $article->getStock(),
            'ean'          => $article->getEan(),
            'itemUnit'     => "",
            'packunit'     => "",

            // price information
            'price'        => $this->articlePriceService->formatPrice( $price / $quantity ),
            'netprice'     => ( $priceNet / $quantity ),
            'amount'       => $this->articlePriceService->formatPrice( round( $price, 2 ) ),
            'amountnet'    => $this->articlePriceService->formatPrice( round( $priceNet, 2 ) ),
            'priceNumeric' => ( $price / $quantity ),
            'tax'          => $this->articlePriceService->formatPrice( round( ( $price ) - ( $priceNet ), 2 ) ),

            // our attributes
            'atsd_configurator_selection_id'     => $selection->getId(),
            'atsd_configurator_selection_master' => $master
        );

        // return it
        return $item;
    }

}
