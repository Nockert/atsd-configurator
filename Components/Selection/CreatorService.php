<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components\Selection;

use Shopware\CustomModels\AtsdConfigurator\Configurator;
use Shopware\CustomModels\AtsdConfigurator\Selection;
use Shopware\Components\Model\ModelManager;
use Shopware\AtsdConfigurator\Components\Helper\CustomerService;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class CreatorService
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
     * @var CustomerService
     */

    protected $customerService;



    /**
     * ...
     *
     * @param ModelManager      $modelManager
     * @param CustomerService   $customerService
     *
     * @return CreatorService
     */

    public function __construct( ModelManager $modelManager, CustomerService $customerService )
    {
        // set params
        $this->modelManager    = $modelManager;
        $this->customerService = $customerService;
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
        // create a new selection
        $selection = new Selection();

        // set default values
        $selection->setConfigurator( $configurator );
        $selection->setCustomer( $this->customerService->getCustomer() );
        $selection->setManual( $manual );

        // save it
        $this->modelManager->persist( $selection );
        $this->modelManager->flush( $selection );

        // loop the articleids
        foreach ( $articles as $articleId => $quantity )
        {
            // get the article
            /* @var $configuratorArticle Configurator\Fieldset\Element\Article */
            $configuratorArticle = $this->modelManager
                ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article' )
                ->findOneBy( array( 'id' => (integer) $articleId ) );

            // not found?
            if ( !$configuratorArticle instanceof Configurator\Fieldset\Element\Article )
                // next
                continue;

            // create a new selection article
            $article = new Selection\Article();

            // set it up
            $article->setQuantity( $quantity );
            $article->setSelection( $selection );
            $article->setArticle( $configuratorArticle );

            // save it
            $this->modelManager->persist( $article );
        }

        // save every article
        $this->modelManager->flush();

        // reload the selection with articles
        $this->modelManager->refresh( $selection );

        // and return it
        return $selection;
    }





}



