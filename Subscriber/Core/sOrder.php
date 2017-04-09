<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConcerto
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber\Core;

use Shopware\Components\DependencyInjection\Container;
use Shopware_Plugins_Frontend_AtsdConfigurator_Bootstrap as Bootstrap;
use Enlight\Event\SubscriberInterface;
use Shopware\AtsdConfigurator\Components\AtsdConfigurator as Component;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class sOrder implements SubscriberInterface
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
	 * Main plugin component.
	 *
	 * @var Component
	 */

	protected $component;






	/**
	 * ...
	 *
	 * @param Bootstrap   $bootstrap
	 * @param Container   $container
	 * @param Component   $component
	 *
	 * @return \Shopware\AtsdConfigurator\Subscriber\Core\sOrder
	 */

	public function __construct(
        Bootstrap $bootstrap,
		Container $container,
        Component $component )
	{
		// set params
		$this->bootstrap = $bootstrap;
		$this->container = $container;
		$this->component = $component;
	}






	/**
	 * Return the subscribed controller events.
	 *
	 * @return array
	 */

	public static function getSubscribedEvents()
	{
		// return the events
		return array(
            'sOrder::sSaveOrder::before'                      => "beforeSaveOrder",
            'Shopware_Modules_Order_SaveOrder_ProcessDetails' => "onProcessDetails"
		);
	}








    /**
     * ...
     *
     * @param \Enlight_Event_EventArgs   $arguments
     *
     * @return void
     */

    public function onProcessDetails( \Enlight_Event_EventArgs $arguments )
    {
        // get the basket
        $basket = $arguments->get( "details" );

        // do we want to split?
        $split = (boolean) $this->bootstrap->Config()->get( "splitConfigurator" );
        $attr  = (string)  $this->bootstrap->Config()->get( "saveInAttribute" );



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
            Shopware()->Db()->exec( $query );

            // since shopware 5.2 copies the attributes from s_order_basket items, we have to set the attribute
            // manually via sql update after the complete process.
            if ( $this->bootstrap->isShopware51() == true )
                // next
                continue;



            // get the detail id
            // $id = (integer) $article['orderDetailId'];

            // get the attribute loader
            /* @var $attributeLoader \Shopware\Bundle\AttributeBundle\Service\DataLoader */
            // $attributeLoader = $this->container->get( "shopware_attribute.data_loader" );

            // read the data
            // $attributeData = $attributeLoader->load( "s_order_basket_attributes", $id );



            // try to insert the split string into attributes
            try
            {
                // try it
                $query = "
                    UPDATE s_order_details_attributes
                    SET " . str_replace( array( "'", '"' ), "", $attr ) . " = :attribute
                    WHERE detailID = :id
                ";
                Shopware()->Db()->query( $query, array( 'id' => (integer) $article['orderDetailId'], 'attribute' => $article['atsd_configurator_split_string'] ) );
            }
            // ignore exceptions for faulty attribute names
            catch ( \Exception $exception ) {}
        }



        // done
        return;
    }








    /**
	 * Split the configurator into single articles.
	 *
	 * @param \Enlight_Hook_HookArgs   $arguments
	 *
	 * @return void
	 */

	public function beforeSaveOrder( \Enlight_Hook_HookArgs $arguments )
	{
        // get the controller
        /* @var $sOrder \sOrder */
        $sOrder = $arguments->getSubject();

        // split items and save attribute
        $this->parseOrderBasket( $sOrder );

        // done
        return;
	}








    /**
     * ...
     *
     * @param \sOrder   $sOrder
     *
     * @return void
     */

    private function parseOrderBasket( \sOrder $sOrder )
    {
        // do we want to split?
        $split = (boolean) $this->bootstrap->Config()->get( "splitConfigurator" );
        $attr  = (string)  $this->bootstrap->Config()->get( "saveInAttribute" );



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
            /* @var $selection \Shopware\CustomModels\AtsdConfigurator\Selection */
            $selection = Shopware()->Models()
                ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Selection' )
                ->find( $selectionId );

            // get basket data
            $configurator = $this->component->getParsedConfiguratorForSelectionBySelection( $selection, false );



            // the selection array
            $selectionArr = array();

            // loop all articles
            /* @var $article \Shopware\CustomModels\AtsdConfigurator\Selection\Article */
            foreach ( $selection->getArticles() as $article )
                // add it
                $selectionArr[$article->getArticle()->getId()] = $article->getQuantity();



            // do we want to split?!
            if ( $split == true )
            {
                // get the main article
                /* @var $article \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct */
                $article = $configurator['article'];

                // get the corrected item
                $item = $this->getBasketItem( $selection, $article, 1, 0, true, (integer) $item['id'] );
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
                        /* @var $article \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct */
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

                        // get the item
                        $splitArticleItem = $this->getBasketItem(
                            $selection,
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
                // set for shopware 5.1
                $item['ob_' . $attr] = implode( "\n", $attribute );

                // set for shopware 5.2
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

        // done
        return;
    }







    /**
     * ...
     *
     * @param \Shopware\CustomModels\AtsdConfigurator\Selection      $selection
     * @param \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct   $article
     * @param integer                                                $quantity
     * @param integer                                                $rebate
     * @param boolean                                                $master
     * @param integer                                                $basketId
     *
     * @return array
     */

    private function getBasketItem( \Shopware\CustomModels\AtsdConfigurator\Selection $selection, \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct $article, $quantity, $rebate, $master, $basketId )
    {
        // get price data
        $price    = ( ( $master == false ) or ( $master == true && $selection->getConfigurator()->getChargeArticle() == true ) ) ? $this->component->getArticlePrice( $article, $quantity ) * ( ( 100 - (integer) $rebate ) / 100 ) : 0.0;
        $priceNet = ( ( $master == false ) or ( $master == true && $selection->getConfigurator()->getChargeArticle() == true ) ) ? $this->component->getArticleNetPrice( $article, $quantity ) * ( ( 100 - (integer) $rebate ) / 100 ) : 0.0;

        // default item
        $item = array(

            // default data
            'id'           => ( $this->bootstrap->isShopware51() == true ) ? null : $basketId,
            'articleID'    => $article->getId(),
            'articlename'  => $article->getName(),
            'ordernumber'  => $article->getNumber(),
            'shippingfree' => $article->isShippingFree(),
            'quantity'     => $quantity,
            'modus'        => "0",
            'esdarticle'   => false,
            'tax_rate'     => $this->component->getTaxRate( $article->getTax()->getTax() ),
            'taxID'        => $article->getTax()->getId(),
            'instock'      => $article->getStock(),
            'ean'          => $article->getEan(),
            'itemUnit'     => "",
            'packunit'     => "",

            // price information
            'price'        => $this->component->formatPrice( $price / $quantity ),
            'netprice'     => ( $priceNet / $quantity ),
            'amount'       => $this->component->formatPrice( round( $price, 2 ) ),
            'amountnet'    => $this->component->formatPrice( round( $priceNet, 2 ) ),
            'priceNumeric' => ( $price / $quantity ),
            'tax'          => $this->component->formatPrice( round( ( $price ) - ( $priceNet ), 2 ) ),

            // our attributes
            'atsd_configurator_selection_id'     => $selection->getId(),
            'atsd_configurator_selection_master' => $master
        );

        // return it
        return $item;
    }





}