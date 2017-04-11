<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components\Selection;

use Shopware\AtsdConfigurator\Components\Exception;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\CustomModels\AtsdConfigurator\Selection;
use Shopware\AtsdConfigurator\Components\Configurator\ArticlePriceService;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class CalculatorService
{

    /**
     * ...
     *
     * @var ParserService
     */

    protected $parserService;



    /**
     * ...
     *
     * @var ArticlePriceService
     */

    protected $articlePriceService;



    /**
     * ...
     *
     * @param ParserService         $parserService
     * @param ArticlePriceService   $articlePriceService
     *
     * @return CalculatorService
     */

    public function __construct( ParserService $parserService, ArticlePriceService $articlePriceService )
    {
        // set params
        $this->parserService       = $parserService;
        $this->articlePriceService = $articlePriceService;
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

    public function calculateSelectionData( $configuratorId, array $selection, $key = null, $validate = true, $includeMaster = true )
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
        $configurator = $this->parserService->getParsedConfiguratorForSelection( $configuratorId, $selection, $validate, $includeMaster );

        // validation failed
        if ( $configurator === null )
            // throw validator exception
            throw new Exception\ValidatorException( "error validating the selection" );



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
                'price'    => ( $configurator['chargeArticle'] == true ) ? $this->articlePriceService->getArticlePrice( $article, 1, $configurator['rebate'] ) : 0.0,
                'priceNet' => ( $configurator['chargeArticle'] == true ) ? $this->articlePriceService->getArticleNetPrice( $article, 1, $configurator['rebate'] ) : 0.0
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
                    $quantity = (integer) $selection[$article['id']];
                    $rebate   = (integer) $configurator['rebate'];

                    // article struct
                    /* @var $articleStruct Struct\ListProduct */
                    $articleStruct = $article['article'];

                    // add weight
                    $return['weight'] += $quantity * $articleStruct->getWeight();

                    // set stock
                    $return['stock'] = min( $return['stock'], floor( $articleStruct->getStock() / $quantity ) );

                    // price calculations
                    $price    = $this->articlePriceService->getArticlePrice( $articleStruct, $quantity );
                    $priceNet = $this->articlePriceService->getArticleNetPrice( $articleStruct, $quantity );

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
            $price    = ( $configurator['chargeArticle'] == true ) ? $this->articlePriceService->getArticlePrice( $article, 1 ) : 0.0;
            $priceNet = ( $configurator['chargeArticle'] == true ) ? $this->articlePriceService->getArticleNetPrice( $article, 1 ) : 0.0;

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
     * @param Selection   $selection
     *
     * @return array
     */

    public function calculateSelectionDataBySelection( Selection $selection )
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


}



