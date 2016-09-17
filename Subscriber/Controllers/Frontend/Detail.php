<?php

/**
 * Aquatuning Software Development - Configurator - Subscriber
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Subscriber\Controllers\Frontend;



/**
 * Aquatuning Software Development - Configurator - Subscriber
 */

class Detail implements \Enlight\Event\SubscriberInterface
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
     * The main component.
     *
     * @var \Shopware\AtsdConfigurator\Components\AtsdConfigurator
     */

    private $component;



    /**
     * Shopware model manager.
     *
     * @var \Shopware\Components\Model\ModelManager
     */

    protected $modelManager;





    /**
	 * ...
	 *
     * @param \Shopware_Components_Plugin_Bootstrap                $bootstrap
     * @param \Shopware\Components\DependencyInjection\Container   $container
	 *
	 * @return \Shopware\AtsdConfigurator\Subscriber\Controllers\Frontend\Detail
	 */

    public function __construct( \Shopware_Components_Plugin_Bootstrap $bootstrap, \Shopware\Components\DependencyInjection\Container $container )
	{
        // set params
        $this->bootstrap = $bootstrap;
        $this->container = $container;

        // set container params
        $this->component    = $this->container->get( "atsd_configurator.component" );
        $this->modelManager = $this->container->get( "shopware.model_manager" );
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
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => "onPostDispatch"
		);
	}








    /**
     * ...
     *
     * @param \Enlight_Event_EventArgs   $arguments
     *
     * @return void
     */

    public function onPostDispatch ( \Enlight_Event_EventArgs $arguments )
    {
        // get the controller
        /* @var $controller \Shopware_Controllers_Frontend_Detail */
        $controller = $arguments->get( "subject" );

        // get parameters
        $request    = $controller->Request();
        $response   = $controller->Response();
        $view       = $controller->View();

        // always add our template directory to make it into the cache
        $view->addTemplateDir( $this->bootstrap->Path() . "Views/" );

        // assign the status
        $view->assign( "atsdConfiguratorShopStatus", (boolean) $this->bootstrap->Config()->get( "shopStatus" ) );

        // only on index
        if ( $request->getActionName() != "index" )
            // nothing to do
            return;



        // get the article
        $article = $view->getAssign( "sArticle" );

        // get the attribute
        /* @var $attribute \Shopware\Bundle\StoreFrontBundle\Struct\Attribute */
        $attribute = $article['attributes']['atsd_configurator'];

        // not set?! should never happen
        if ( !$attribute instanceof \Shopware\Bundle\StoreFrontBundle\Struct\Attribute )
            // abort
            return;

        // do we have a configurator?
        if ( (boolean) $attribute->get( "hasConfigurator" ) == false )
            // nothing to do
            return;



        // get the configurator query builder
        $builder = $this->component->getRepository()->getPartialConfiguratorWithArticlesQueryBuilder();

        // only for our product
        $builder->andWhere( "linkedArticle.id = :articleId" )
            ->setParameter( "articleId", $article['articleID'] );

        // get them all
        $configurators = $builder->getQuery()->getArrayResult();

        // even found?!
        if ( count( $configurators ) == 0 )
            // shouldnt happen
            return;

        // get ours
        $configurator = $configurators[0];



        // validate it
        $valid = $this->component->valdiateConfigurator( $configurator );



        // set the main status
        $view->assign( "atsdConfiguratorStatus", true );



        // is the configurator invalid?!
        if ( $valid == false )
        {
            // set the view
            $view->assign( "atsdConfiguratorInvalid", true );

            // nothing more to do
            return;
        }



        // filter the configurator
        $configurator = $this->component->filterConfigurator( $configurator );

        // get all product infos
        $configurator = $this->component->parseConfigurator( $configurator );



        // try getting the current selection
        $selection = $this->getCurrentSelection( $controller->Request() );

        // did we find it?
        if ( ( is_array( $selection ) ) and ( count( $selection ) > 0 ) )
        {
            // was it loaded?
            if ( $controller->Request()->has( "atsdConfiguratorLoaded" ) )
                // set it
                $view->assign( "atsdConfiguratorSelectionLoaded", true );

            // validate it
            if ( $this->component->validateSelection( $configurator, $selection ) == false )
            {
                // set the view
                $view->assign( "atsdConfiguratorSelectionError", true );
            }
        }



        // no selection found
        if ( ( !is_array( $selection ) ) or ( count( $selection ) == 0 ) )
            // get default selection for this configurator
            $selection = $this->component->getDefaultSelection( $configurator['id'] );



        // did we just save it?
        if ( $controller->Request()->has( "atsdConfiguratorSaved" ) )
        {
            // assign stuff
            $view->assign( "atsdConfiguratorSelectionSaved", true );
            $view->assign( "atsdConfiguratorSelectionSavedKey", $controller->Request()->getParam( "atsdConfiguratorKey" ) );
        }



        // assign everything
        $view->assign( "atsdConfigurator", $configurator );
        $view->assign( "atsdConfiguratorSelection", $selection );
        $view->assign( "atsdConfiguratorConfigArticleLinkStatus", (boolean) $this->bootstrap->Config()->get( "articleLinkStatus" ) );
        $view->assign( "atsdConfiguratorConfigNoChoicePosition", (integer) $this->bootstrap->Config()->get( "noChoicePosition" ) );

        // and we are done
        return;
    }






    /**
     * ...
     *
     * @param \Enlight_Controller_Request_Request   $request
     *
     * @return array
     */

    private function getCurrentSelection( \Enlight_Controller_Request_Request $request )
    {
        // try getting the key from request
        $key = (string) $request->getParam( "atsdConfiguratorKey" );

        // none given?
        if ( empty( $key ) )
            // nothing to do
            return array();

        // try to find the selection
        /* @var $selection \Shopware\CustomModels\AtsdConfigurator\Selection */
        $selection = $this->modelManager
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Selection' )
            ->findOneBy( array( 'key' => $key ) );

        // not found?
        if ( !$selection instanceof \Shopware\CustomModels\AtsdConfigurator\Selection )
            // nope
            return array();

        // the ids
        $arr = array();

        // get the articles
        $articles = $selection->getArticles();

        // loop the selection
        /* @var $article \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article */
        foreach ( $articles as $article )
            // add it
            array_push( $arr, $article->getId() );

        // return them
        return $arr;
    }






}