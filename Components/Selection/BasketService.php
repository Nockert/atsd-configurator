<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components\Selection;

use Shopware\Components\Model\ModelManager;
use Shopware\CustomModels\AtsdConfigurator\Selection;
use Shopware\Models\Order\Basket;
use Shopware\Models\Attribute\OrderBasket as Attribute;
use Enlight_Components_Session_Namespace as Session;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\AtsdConfigurator\Components\Exception\ValidatorException;
use Shopware\AtsdConfigurator\Components\AtsdConfigurator;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class BasketService
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
     * @var Session
     */

    protected $session;



    /**
     * Shopware context service.
     *
     * @var ContextService
     */

    protected $contextService;



    /**
     * ...
     *
     * @var AtsdConfigurator
     */

    protected $component;



    /**
     * ...
     *
     * @param ModelManager       $modelManager
     * @param Session            $session
     * @param ContextService     $contextService
     * @param AtsdConfigurator   $component
     */

    public function __construct( ModelManager $modelManager, Session $session, ContextService $contextService, AtsdConfigurator $component )
    {
        // set params
        $this->modelManager   = $modelManager;
        $this->session        = $session;
        $this->contextService = $contextService;
        $this->component      = $component;
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
        // get the article
        $article = $selection->getConfigurator()->getArticle();

        // get detail
        $detail = $article->getMainDetail();

        // currency
        $currency = $this->getCurrency();



        // create the basket item
        $item = new Basket();

        // persist
        $this->modelManager->persist( $item );

        // set a few parameters
        $item->setSessionId( $this->session->offsetGet( "sessionId" ) );
        $item->setCustomerId( (integer) $this->session->offsetGet( "sUserId" ) );
        $item->setArticleName( $article->getName() );
        $item->setArticleId( $article->getId() );
        $item->setOrderNumber( $detail->getNumber() );
        $item->setShippingFree( $detail->getShippingFree() );
        $item->setQuantity( 1 );
        $item->setDate( new \DateTime() );
        $item->setMode( 0 );
        $item->setEsdArticle( 0 );
        $item->setPartnerId( (string) $this->session->offsetGet( "sPartner" ) );
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
        $attribute = new Attribute();

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
     * @param string   $sessionId
     *
     * @return float
     */

    public function getBasketWeight( $sessionId )
    {
        // we dont need this because the selections are cached within the session which is even faster
        /*
        $query = "
            SELECT *
            FROM s_order_basket AS basket
                LEFT JOIN s_order_basket_attributes AS attribute
                    ON basket.id = attribute.basketID
                LEFT JOIN atsd_configurators_selections_articles AS selectionArticle
                    ON attribute.atsd_configurator_selection_id = selectionArticle.selectionId
                LEFT JOIN atsd_configurators_fieldsets_elements_articles AS elementArticle
                    ON selectionArticle.articleId = elementArticle.id
                LEFT JOIN s_articles_details AS article
                    ON elementArticle.articleId = article.articleID AND article.kind = 1
            WHERE basket.sessionID = :sessionId
                AND attribute.atsd_configurator_selection_id IS NOT NULL
        ";
        */



        // weight
        $weight = 0.0;



        // get every article again
        $query = "
            SELECT *
            FROM s_order_basket AS basket
                LEFT JOIN s_order_basket_attributes AS attribute
                    ON basket.id = attribute.basketID
            WHERE basket.sessionID = :sessionId
                AND attribute.atsd_configurator_selection_id IS NOT NULL
        ";
        $articles = Shopware()->Db()->fetchAll( $query, array( 'sessionId' => $sessionId ) );

        // loop them
        foreach ( $articles as $article )
        {
            // get the selection
            /* @var $selection \Shopware\CustomModels\AtsdConfigurator\Selection */
            $selection = Shopware()->Models()
                ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Selection' )
                ->find( (integer) $article['atsd_configurator_selection_id'] );

            // get selection data and ignore it when an error occurs
            try
            {
                // get the selection data
                $data = $this->component->getSelectionData( $selection );
            }
                // catch validation errors
            catch ( ValidatorException $exception )
            {
                // set 0 as weight
                $data = array( 'weight' => 0 );
            }

            // add the weight
            $weight = (float) $weight + (float) $data['weight'];
        }



        // return the configurator weight
        return $weight;
    }








    /**
     * Returns the current currency.
     *
     * @return Struct\Currency
     */

    private function getCurrency()
    {
        // return via context service
        return $this->contextService->getShopContext()->getCurrency();
    }






    /**
     * Get the tax rate for a tax id.
     *
     * @param integer   $id
     *
     * @return float
     */

    private function getTaxRate( $id )
    {
        // call the sarticles module
        return (float) Shopware()->Modules()->Articles()->getTaxRateByConditions( $id );
    }





}



