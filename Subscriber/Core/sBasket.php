<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConcerto
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Subscriber\Core;

use Enlight\Event\SubscriberInterface;
use AtsdConfigurator\Components\Exception\ValidatorException;
use AtsdConfigurator\Components\AtsdConfigurator as Component;
use Enlight_Event_EventArgs as EventArgs;
use Enlight_Hook_HookArgs as HookArgs;
use Shopware\Models\Article\Detail;
use AtsdConfigurator\Models\Selection;
use AtsdConfigurator\Models\Configurator;
use Shopware\Models\Order\Basket as BasketItem;
use Shopware\Components\Model\ModelManager;
use Enlight_Components_Db_Adapter_Pdo_Mysql as Db;
use sBasket as CoreClass;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class sBasket implements SubscriberInterface
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
	 * Main plugin component.
	 *
	 * @var Component
	 */

	protected $component;



    /**
     * ...
     *
     *
     * @param ModelManager   $modelManager
     * @param Db             $db
     * @param Component      $component
     */

	public function __construct( ModelManager $modelManager, Db $db, Component $component )
    {
		// set params
		$this->modelManager = $modelManager;
		$this->db           = $db;
		$this->component    = $component;
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
	 * @param EventArgs   $arguments
	 *
	 * @return string
	 */

	public function onBasketGetBasketFilterSqlEvent( EventArgs $arguments )
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
     * @param EventArgs   $arguments
     *
     * @return array
     */

    public function onFilterPrice( EventArgs $arguments )
    {
        // get the array
        $queryNewPrice = $arguments->getReturn();

        // get the id
        $id = (integer) $arguments->get( "id" );



        // get the selection id
        $query = "
            SELECT atsd_configurator_selection_id
            FROM s_order_basket_attributes
            WHERE basketID = ?
        ";
        $selectionId = (integer) $this->db->fetchOne( $query, array( $id ) );



        // no selection?
        if ( $selectionId == 0 )
            // nothing to do
            return $queryNewPrice;



        // get the selection
        /* @var $selection Selection */
        $selection = $this->modelManager
            ->getRepository( Selection::class )
            ->find( $selectionId );

        // try to get selection data
        try
        {
            // try it
            $data = $this->component->getSelectionData( $selection );

        }
        // catch validation errors
        catch ( ValidatorException $exception )
        {
            // just return invalid array and sbasket will remove the article
            return array();
        }

        // set the new price
        $queryNewPrice['price'] = $data['priceNet'];

        // return it
        return $queryNewPrice;
    }



    /**
     * Dont allow an article with a configurator to be added like this.
     *
     * @param HookArgs   $arguments
     *
     * @return void
     */

    public function beforeAddArticleHook( HookArgs $arguments )
    {
        // get the controller
        /* @var $sBasket CoreClass */
        $sBasket = $arguments->getSubject();

        // get parameters
        $ordernumber = (string)  $arguments->get( "id" );
        $quantity    = (integer) $arguments->get( "quantity" );

        // get the detail
        /* @var $detail Detail */
        $detail = $this->modelManager
            ->getRepository( Detail::class )
            ->findOneBy( array( 'number' => $ordernumber ) );

        // did we even find it?
        if ( !$detail instanceof Detail )
            // ignore it
            return;

        // get the article
        $article = $detail->getArticle();

        // does it have a configurator?
        /* @var $configurator Configurator */
        $configurator = $this->modelManager
            ->getRepository( Configurator::class )
            ->findOneBy( array( 'article' => $article ) );

        // no configurator found?
        if ( !$configurator instanceof Configurator )
            // nothing to do
            return;

        // disable default addArticle()
        $arguments->set( "id", "0" );
    }



    /**
     * Read our item data for any article if available.
     *
     * @param EventArgs   $arguments
     *
     * @return string
     */

    public function onBasketGetBasketFilterResultEvent( EventArgs $arguments )
    {
        // get the basket
        $basket = $arguments->getReturn();

        // loop all items in the basket
        foreach ( $basket['content'] as $key => $basketArticle )
        {
            // default
            $basket['content'][$key]['atsdConfiguratorHasSelection'] = false;

            // get the selector id
            $selectorId = (integer) $basketArticle['atsd_configurator_selection_id'];

            // is this a selector?
            if ( $selectorId == 0 )
                // it isnt
                continue;

            // get the selector
            /* @var $selection Selection */
            $selection = $this->modelManager
                ->getRepository( Selection::class )
                ->find( $selectorId );



            // try to get selection data
            try
            {
                // try it
                $data = $this->component->getSelectionData( $selection );

            }
            // catch validation errors
            catch ( ValidatorException $exception )
            {
                // remove the article
                unset( $basket['content'][$key] );

                // remove it from the db
                $this->removeSelectionFromBasket( $basketArticle['id'] );

                // continue with next one
                continue;
            }



            // set the data
            $basket['content'][$key]['atsdConfiguratorHasSelection'] = true;
            $basket['content'][$key]['atsdConfiguratorSelection']    = $data;

            // overwrite default values for the basket
            $basket['content'][$key]['instock'] = $data['stock'];
        }

        // return the new basket
        return $basket;
    }



    /**
     * ...
     *
     * @param integer   $orderBasketId
     *
     * @return void
     */

    private function removeSelectionFromBasket( $orderBasketId )
    {
        // get the model to remove the attribute as well
        /* @var $orderBasket BasketItem */
        $orderBasket = $this->modelManager->find( BasketItem::class, $orderBasketId );

        // remove it
        $this->modelManager->remove( $orderBasket );
        $this->modelManager->flush( $orderBasket );
    }

}
