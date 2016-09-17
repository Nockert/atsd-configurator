<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConcerto
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber\Core;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class sOrder implements \Enlight\Event\SubscriberInterface
{

	/**
	 * Main bootstrap object.
	 *
	 * @var \Shopware_Components_Plugin_Bootstrap
	 */

	protected $bootstrap;



	/**
	 * DI container.
	 *
	 * @var \Shopware\Components\DependencyInjection\Container
	 */

	protected $container;



	/**
	 * Main plugin component.
	 *
	 * @var \Shopware\AtsdConfigurator\Components\AtsdConfigurator
	 */

	protected $component;






	/**
	 * ...
	 *
	 * @param \Shopware_Components_Plugin_Bootstrap                    $bootstrap
	 * @param \Shopware\Components\DependencyInjection\Container       $container
	 * @param \Shopware\AtsdConfigurator\Components\AtsdConfigurator   $component
	 *
	 * @return \Shopware\AtsdConfigurator\Subscriber\Core\sOrder
	 */

	public function __construct(
		\Shopware_Components_Plugin_Bootstrap $bootstrap,
		\Shopware\Components\DependencyInjection\Container $container,
		\Shopware\AtsdConfigurator\Components\AtsdConfigurator $component )
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



            // do we want to split?!
            if ( $split == true )
            {
                // get the main article
                /* @var $article \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct */
                $article = $configurator['article'];

                // get the corrected item
                $item = $this->getBasketItem( $selection, $article, 1, 0, true );
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
                        $article  = $articleArr['article'];

                        // our article string
                        array_push(
                            $elementArticles,
                            $articleArr['quantity'] . "x " . $article->getNumber() . " " . $article->getName()
                        );

                        // we do NOT want to split?
                        if ( $split == false )
                            // next one
                            continue;

                        // get the item
                        $splitArticleItem = $this->getBasketItem(
                            $selection,
                            $article,
                            (integer) $articleArr['quantity'],
                            (integer) $configurator['rebate'],
                            false
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
                // set it
                $item['ob_' . $attr] = implode( "\n", $attribute );



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
     *
     * @return array
     */

    private function getBasketItem( \Shopware\CustomModels\AtsdConfigurator\Selection $selection, \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct $article, $quantity, $rebate, $master )
    {
        // get price data
        $price    = $this->component->getArticlePrice( $article ) * ( ( 100 - (integer) $rebate ) / 100 );
        $priceNet = $this->component->getArticleNetPrice( $article ) * ( ( 100 - (integer) $rebate ) / 100 );

        // default item
        $item = array(

            // default data
            'id' => null,
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
            'price'        => $this->component->formatPrice( $price ),
            'netprice'     => $priceNet,
            'amount'       => $this->component->formatPrice( round( $price * $quantity, 2 ) ),
            'amountnet'    => $this->component->formatPrice( round( $priceNet * $quantity, 2 ) ),
            'priceNumeric' => $price,
            'tax'          => $this->component->formatPrice( round( ( $price * $quantity ) - ( $priceNet * $quantity ), 2 ) ),

            // our attributes
            'atsd_configurator_selection_id'     => $selection->getId(),
            'atsd_configurator_selection_master' => $master
        );

        // return it
        return $item;
    }





}