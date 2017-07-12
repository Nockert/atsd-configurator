<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components\Configurator;

use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\MediaBundle\MediaService;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\AtsdConfigurator\Components\VersionService;


/**
 * Aquatuning Software Development - Configurator - Component
 */

class ParserService
{

    /**
     * ...
     *
     * @var VersionService
     */

    protected $versionService;



    /**
     * ...
     *
     * @var ListProductServiceInterface
     */

    protected $listProductService;



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
     * ...
     *
     * @param VersionService                $versionService
     * @param ListProductServiceInterface   $listProductService
     * @param ContextService                $contextService
     */

    public function __construct( VersionService $versionService, ListProductServiceInterface $listProductService, ContextService $contextService, MediaService $mediaService )
    {
        // set params
        $this->versionService     = $versionService;
        $this->listProductService = $listProductService;
        $this->contextService     = $contextService;
        $this->mediaService       = $mediaService;
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

    public function parse( array $configurator, $includeMaster = true )
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



        // get all products
        $products = $this->listProductService->getList(
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
                    // current article number
                    $number = $article['article']['mainDetail']['number'];

                    // do we have a product?
                    if ( !isset( $products[$number] ) )
                    {
                        // remove it
                        unset( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey]['articles'][$articleKey] );

                        // next
                        continue;
                    }

                    // get the product
                    $product = $products[$number];

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




}



