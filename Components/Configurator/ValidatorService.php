<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components\Configurator;

/**
 * Aquatuning Software Development - Configurator - Component
 */

class ValidatorService
{

    /**
     * ...
     *
     * @var StockService
     */

    protected $stockService;



    /**
     * ...
     *
     * @param StockService   $stockService
     */

    public function __construct( StockService $stockService )
    {
        // set params
        $this->stockService = $stockService;
    }







    /**
     * Validate a configurator and check if its even possible to configure it or if
     * mandatory articles arent available.
     *
     * @param array   $configurator
     *
     * @return boolean
     */

    public function valdiate( array $configurator )
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
                    $maxStock = $this->stockService->getMaxArticleStock( $article['article']['mainDetail']['inStock'], $article['article']['lastStock'], $article['quantity'] );

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







}



