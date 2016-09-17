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

class sBasket implements \Enlight\Event\SubscriberInterface
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
	 * @return \Shopware\AtsdConfigurator\Subscriber\Core\sBasket
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
			'Shopware_Modules_Basket_GetBasket_FilterSQL'                  => "onBasketGetBasketFilterSqlEvent",
            'Shopware_Modules_Basket_GetBasket_FilterResult'               => "onBasketGetBasketFilterResultEvent",
            'Shopware_Modules_Basket_getPriceForUpdateArticle_FilterPrice' => "onFilterPrice",
            'sBasket::sAddArticle::before'                                 => "beforeAddArticleHook"
		);
	}







	/**
	 * Read our plugin attributes as well.
	 *
	 * @param \Enlight_Event_EventArgs   $arguments
	 *
	 * @return string
	 */

	public function onBasketGetBasketFilterSqlEvent( \Enlight_Event_EventArgs $arguments )
	{
		// get the query
		$query = $arguments->getReturn();

		// add our attribute
		$query = str_replace(
			"s_order_basket_attributes.attribute6 as ob_attr6",
			"s_order_basket_attributes.attribute6 as ob_attr6, s_order_basket_attributes.atsd_configurator_selection_id",
			$query
		);

		// return it
		return $query;
	}






    /**
     * Set our own price for every selection.
     *
     * @param \Enlight_Event_EventArgs   $arguments
     *
     * @return array
     */

    public function onFilterPrice( \Enlight_Event_EventArgs $arguments )
    {
        // get the array
        $queryNewPrice = $arguments->getReturn();

        // get the id
        $id = (integer) $arguments->get( "id" );

        // get the basket
        /* @var $basket \Shopware\Models\Order\Basket */
        $basket = Shopware()->Models()
            ->find( '\Shopware\Models\Order\Basket', $id );

        // not found?
        if ( !$basket instanceof \Shopware\Models\Order\Basket )
            // nope
            return $queryNewPrice;

        // get the attribute
        /* @var $attribute \Shopware\Models\Attribute\OrderBasket */
        $attribute = $basket->getAttribute();

        // we have no attribute?!
        if ( !$attribute instanceof \Shopware\Models\Attribute\OrderBasket )
            // stop
            return $queryNewPrice;

        // get the selection id
        $selectionId = (integer) $attribute->getAtsdConfiguratorSelectionId();

        // no selection?
        if ( $selectionId == 0 )
            // nothing to do
            return $queryNewPrice;

        // get the selection
        /* @var $selection \Shopware\CustomModels\AtsdConfigurator\Selection */
        $selection = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Selection' )
            ->find( $selectionId );

        // get selection data
        $data = $this->component->getSelectionData( $selection );

        // set the new price
        $queryNewPrice['price'] = $data['priceNet'];

        // return it
        return $queryNewPrice;
    }







    /**
     * Dont allow an article with a configurator to be added like this.
     *
     * @param \Enlight_Hook_HookArgs   $arguments
     *
     * @return void
     */

    public function beforeAddArticleHook( \Enlight_Hook_HookArgs $arguments )
    {
        // get the controller
        /* @var $sBasket \sBasket */
        $sBasket = $arguments->getSubject();

        // get parameters
        $ordernumber = (string)  $arguments->get( "id" );
        $quantity    = (integer) $arguments->get( "quantity" );

        // get the detail
        /* @var $detail \Shopware\Models\Article\Detail */
        $detail = Shopware()->Models()
            ->getRepository( '\Shopware\Models\Article\Detail' )
            ->findOneBy( array( 'number' => $ordernumber ) );

        // did we even find it?
        if ( !$detail instanceof \Shopware\Models\Article\Detail )
            // ignore it
            return;

        // get the article
        $article = $detail->getArticle();

        // does it have a configurator?
        $configurator = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator' )
            ->findOneBy( array( 'article' => $article ) );

        // no configurator found?
        if ( !$configurator instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator )
            // nothing to do
            return;



        // disable default addArticle()
        $arguments->set( "id", "0" );

        //done here
        return;


    }






    /**
     * Read our item data for any article if available.
     *
     * @param \Enlight_Event_EventArgs   $arguments
     *
     * @return string
     */

    public function onBasketGetBasketFilterResultEvent( \Enlight_Event_EventArgs $arguments )
    {
        // get the basket
        $basket = $arguments->getReturn();

        // loop all items in the basket
        foreach ( $basket['content'] as &$basketArticle )
        {
            // default
            $basketArticle['atsdConfiguratorHasSelection'] = false;

            // get the selector id
            $selectorId = (integer) $basketArticle['atsd_configurator_selection_id'];

            // is this a selector?
            if ( $selectorId == 0 )
                // it isnt
                continue;

            // get the selector
            /* @var $selection \Shopware\CustomModels\AtsdConfigurator\Selection */
            $selection = Shopware()->Models()
                ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Selection' )
                ->find( $selectorId );

            // get basket data
            $data = $this->component->getSelectionData( $selection );

            // set the data
            $basketArticle['atsdConfiguratorSelection']    = $data;
            $basketArticle['atsdConfiguratorHasSelection'] = true;

            // overwrite default values for the basket
            $basketArticle['instock'] = $data['stock'];
        }

        // return the new basket
        return $basket;
    }











}