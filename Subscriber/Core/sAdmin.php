<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConcerto
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber\Core;

use Shopware\AtsdConfigurator\Components\Exception\ValidatorException;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class sAdmin implements \Enlight\Event\SubscriberInterface
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
	 * @return \Shopware\AtsdConfigurator\Subscriber\Core\sAdmin
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
			'sAdmin::sGetDispatchBasket::after' => "afterGetDispatchBasket"
		);
	}







	/**
	 * Add the weight to the dispatch basket to get correct shipping costs.
	 *
	 * @param \Enlight_Hook_HookArgs   $arguments
	 *
	 * @return array
	 */

	public function afterGetDispatchBasket( \Enlight_Hook_HookArgs $arguments )
	{
		// get the query
		$basket = $arguments->getReturn();

        // get every article again
        $query = "
            SELECT *
            FROM s_order_basket AS basket
                LEFT JOIN s_order_basket_attributes AS attribute
                    ON basket.id = attribute.basketID
            WHERE basket.sessionID = :sessionId
                AND attribute.atsd_configurator_selection_id IS NOT NULL
        ";
        $articles = Shopware()->Db()->fetchAll( $query, array( 'sessionId' => $basket['sessionID'] ) );

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
            $basket['weight'] = (float) $basket['weight'] + (float) $data['weight'];
        }

		// return the correct basket
		return $basket;
	}









}