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
     * @param ModelManager     $modelManager
     * @param Session          $session
     * @param ContextService   $contextService
     */

    public function __construct( ModelManager $modelManager, Session $session, ContextService $contextService )
    {
        // set params
        $this->modelManager   = $modelManager;
        $this->session        = $session;
        $this->contextService = $contextService;
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



