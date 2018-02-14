<?php

/**
 * Aquatuning Software Development - Configurator - Listener
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Listener\Controllers\Frontend;

use Enlight_Event_EventArgs as EventArgs;
use Shopware_Controllers_Frontend_Detail as Controller;
use Enlight_Controller_Request_Request as Request;
use AtsdConfigurator\Models\Selection;
use AtsdConfigurator\Models\Configurator;
use AtsdConfigurator\Models\Repository;
use AtsdConfigurator\Components;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;



/**
 * Aquatuning Software Development - Configurator - Listener
 */

class Detail
{

    /**
     * DI container.
     *
     * @var Container
     */

    protected $container;



    /**
     * ...
     *
     * @var ModelManager
     */

    protected $modelManager;



    /**
	 * ...
	 *
	 * @var string
	 */

	protected $viewDir;



    /**
     * ...
     *
     * @var array
     */

    protected $configuration;



    /**
     * ...
     *
     * @param Container      $container
     * @param ModelManager   $modelManager
     * @param string         $viewDir
     * @param array          $configuration
     */

	public function __construct( Container $container, ModelManager $modelManager, $viewDir, array $configuration )
	{
		// set params
        $this->container     = $container;
        $this->modelManager  = $modelManager;
		$this->viewDir       = $viewDir;
		$this->configuration = $configuration;
	}



    /**
     * ...
     *
     * @param EventArgs   $arguments
     *
     * @return void
     */

    public function onPostDispatch ( EventArgs $arguments )
    {
        // get the controller
        /* @var $controller Controller */
        $controller = $arguments->get( "subject" );

        // get parameters
        $request    = $controller->Request();
        $view       = $controller->View();

        // always add our template directory to make it into the cache
        $view->addTemplateDir( $this->viewDir );

        // assign the status
        $view->assign( "atsdConfiguratorShopStatus", (boolean) $this->configuration[ "shopStatus" ] );

        // only on index
        if ( $request->getActionName() != "index" )
            // nothing to do
            return;



        // get the article
        $article = $view->getAssign( "sArticle" );

        // get the attribute
        /* @var $attribute Struct\Attribute */
        $attribute = $article['attributes']['atsd_configurator'];

        // not set?! should never happen
        if ( !$attribute instanceof Struct\Attribute )
            // abort
            return;

        // do we have a configurator?
        if ( (boolean) $attribute->get( "hasConfigurator" ) == false )
            // nothing to do
            return;



        /* @var $repository Repository */
        $repository = $this->modelManager->getRepository( Configurator::class );

        // get the configurator query builder
        $builder = $repository->getPartialConfiguratorWithArticlesQueryBuilder();

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



        /* @var $validatorService Components\Configurator\ValidatorService */
        $validatorService = $this->container->get( "atsd_configurator.configurator.validator_service");

        /* @var $filterService Components\Configurator\FilterService */
        $filterService = $this->container->get( "atsd_configurator.configurator.filter_service");

        /* @var $parserService Components\Configurator\ParserService */
        $parserService = $this->container->get( "atsd_configurator.configurator.parser_service");

        /* @var $selectionValidatorService Components\Selection\ValidatorService */
        $selectionValidatorService = $this->container->get( "atsd_configurator.selection.validator_service");

        /* @var $selectionDefaultService Components\Selection\DefaultService */
        $selectionDefaultService = $this->container->get( "atsd_configurator.selection.default_service");

        /* @var $versionService Components\VersionService */
        $versionService = $this->container->get( "atsd_configurator.version_service");



        // validate it
        $valid = $validatorService->valdiate( $configurator );



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
        $configurator = $filterService->filter( $configurator );

        // get all product infos
        $configurator = $parserService->parse( $configurator );



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
            if ( $selectionValidatorService->validate( $configurator, $selection ) == false )
                // set the view
                $view->assign( "atsdConfiguratorSelectionError", true );
        }



        // no selection found
        if ( ( !is_array( $selection ) ) or ( count( $selection ) == 0 ) )
            // get default selection for this configurator
            $selection = $selectionDefaultService->getDefaultSelection( $configurator['id'] );



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
        $view->assign( "atsdConfiguratorConfigArticleLinkStatus", (boolean) $this->configuration[ "articleLinkStatus" ] );
        $view->assign( "atsdConfiguratorConfigNoChoicePosition", (integer) $this->configuration[ "noChoicePosition" ] );
        $view->assign( "atsdConfiguratorConfigSaleType", (integer) $this->configuration[ "saleType" ] );
        $view->assign( "atsdConfiguratorIsShopware53", $versionService->isShopware53() );
        $view->assign( "atsdConfiguratorShowOneGroup", (boolean) $this->configuration[ "showOneGroup" ] );
    }



    /**
     * ...
     *
     * @param Request   $request
     *
     * @return array
     */

    private function getCurrentSelection( Request $request )
    {
        // try getting the key from request
        $key = (string) $request->getParam( "atsdConfiguratorKey" );

        // none given?
        if ( empty( $key ) )
            // nothing to do
            return array();

        // try to find the selection
        /* @var $selection Selection */
        $selection = $this->modelManager
            ->getRepository( Selection::class )
            ->findOneBy( array( 'key' => $key ) );

        // not found?
        if ( !$selection instanceof Selection )
            // nope
            return array();

        // the ids
        $arr = array();

        // get the articles
        $articles = $selection->getArticles();

        // loop the selection
        /* @var $article Selection\Article */
        foreach ( $articles as $article )
            // add the article
            $arr[$article->getArticle()->getId()] = $article->getQuantity();

        // return them
        return $arr;
    }

}
