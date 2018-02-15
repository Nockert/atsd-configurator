<?php

/**
 * Aquatuning Software Development - Configurator Plugin - Controller
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

use AtsdConfigurator\Models\Configurator;
use AtsdConfigurator\Models\Selection;
use AtsdConfigurator\Components\Exception\ValidatorException;
use AtsdConfigurator\Components;
use Shopware\Components\CSRFWhitelistAware;



class Shopware_Controllers_Frontend_AtsdConfigurator extends Enlight_Controller_Action implements CSRFWhitelistAware
{

    /**
     * ...
     *
     * @return array
     */

    public function getWhitelistedCSRFActions()
    {
        // return all actions
        return array_values( array_filter(
            array_map(
                function( $method ) { return ( substr( $method, -6 ) == "Action" ) ? substr( $method, 0, -6 ) : null; },
                get_class_methods( $this )
            ),
            function ( $method ) { return ( !in_array( (string) $method, array( "", "index", "load", "extends" ) ) ); }
        ));
    }



    /**
     * ...
     *
     * @return void
     */

    public function preDispatch()
    {
        // ...
        $viewDir = $this->container->getParameter( "atsd_configurator.view_dir" );

        // ...
        $this->get( "template" )->addTemplateDir($viewDir);

        // ...
        parent::preDispatch();
    }



    /**
     * Save a new selection and redirect back to the article.
     *
     * @throws Exception
     *
     * @return void
     */

    public function saveSelectionAction()
    {
        // create a new selection
        $selection = $this->createSelectionFromRequest( true );

        // redirect to article again
        $url = array(
            'module'                => "frontend",
            'controller'            => "detail",
            'action'                => "index",
            'sArticle'              => $selection->getConfigurator()->getArticle()->getId(),
            'atsdConfiguratorKey'   => $selection->getKey(),
            'atsdConfiguratorSaved' => 1
        );

        // and redirect
        $this->redirect( $url );
    }




    /**
     * Load a selection and redirect to the article.
     *
     * @throws Exception
     *
     * @return void
     */

    public function loadSelectionAction()
    {
        // try getting the key from request
        $key = (string) $this->Request()->getParam( "key" );

        // none given?
        if ( empty( $key ) )
            // nothing to do
            throw new Exception( "unknown key" );

        // try to find the selection
        /* @var $selection Selection */
        $selection = Shopware()->Models()
            ->getRepository( Selection::class )
            ->findOneBy( array( 'key' => $key ) );

        // not found?
        if ( !$selection instanceof Selection )
            // nope
            throw new Exception( "selection not found for key " . $key );



        // redirect to article
        $url = array(
            'module'                 => "frontend",
            'controller'             => "detail",
            'action'                 => "index",
            'sArticle'               => $selection->getConfigurator()->getArticle()->getId(),
            'atsdConfiguratorKey'    => $selection->getKey(),
            'atsdConfiguratorLoaded' => 1
        );

        // and redirect
        $this->redirect( $url );
    }







    /**
     * Add a configurator to the basket.
     *
     * @throws \Exception
     *
     * @return void
     */

    public function addToBasketAction()
    {
        // get the component
        /* @var $component Components\AtsdConfigurator */
        $component = $this->get( "atsd_configurator.component" );

        // create a new selection
        $selection = $this->createSelectionFromRequest();



        // get the configurator
        $builder = $component->getRepository()->getConfiguratorWithArticlesQueryBuilder();

        // only for our products
        $builder->andWhere( "configurator.id = :id" )
            ->setParameter( "id", $selection->getConfigurator()->getId() );

        // get the results as array
        $configurators = $builder->getQuery()->getArrayResult();

        // not found?
        $configurator = $configurators[0];



        /* @var $validatorService Components\Configurator\ValidatorService */
        $validatorService = $this->container->get( "atsd_configurator.configurator.validator_service");

        /* @var $filterService Components\Configurator\FilterService */
        $filterService = $this->container->get( "atsd_configurator.configurator.filter_service");

        /* @var $parserService Components\Configurator\ParserService */
        $parserService = $this->container->get( "atsd_configurator.configurator.parser_service");

        /* @var $selectionValidatorService Components\Selection\ValidatorService */
        $selectionValidatorService = $this->container->get( "atsd_configurator.selection.validator_service");



        // validate it
        if ( $validatorService->valdiate( $configurator ) == false )
            // done
            throw new \Exception( "invalid configurator with id " . $selection->getConfigurator()->getId() );

        // filter the configurator
        $configurator = $filterService->filter( $configurator );

        // get all product infos
        $configurator = $parserService->parse( $configurator );



        // selector as array
        $selectionArr = array();

        // get articles
        $articles = $selection->getArticles();

        // loop it
        /* @var $article Selection\Article */
        foreach ( $articles as $article )
            // add the article
            $selectionArr[$article->getArticle()->getId()] = $article->getQuantity();



        // is the selection valid?
        if ( $selectionValidatorService->validate( $configurator, $selectionArr ) == false )
        {
            // redirect to article again
            $url = array(
                'module'                 => "frontend",
                'controller'             => "detail",
                'action'                 => "index",
                'sArticle'               => $selection->getConfigurator()->getArticle()->getId(),
                'atsdConfiguratorKey'    => $selection->getKey()
            );

            // go
            $this->redirect( $url );

            // done
            return;
        }



        /* @var $basketService Components\Selection\BasketService */
        $basketService = $this->container->get( "atsd_configurator.selection.basket_service");

        // add it
        $basketService->addSelectionToBasket( $selection );

        // get the data once to cache it
        $component->getSelectionData( $selection );



        // redirect to checkout cart
        $url = array(
            'module'     => "frontend",
            'controller' => "checkout",
            'action'     => "cart"
        );

        // and redirect
        $this->redirect( $url );

    }






    /**
     * ...
     *
     * @return void
     */

    public function getArticleInfoAction()
    {
        // enable json
        Enlight()->Plugins()->Controller()->Json()->setPadding();

        // article id
        $articleId = (integer) $this->Request()->getParam( "articleId" );

        // get quickview infos
        $quickview = (boolean) $this->Request()->getParam( "quickview" );
        $showDescription = (boolean) $this->Request()->getParam( "showDescription" );
        $showAttributes = (boolean) $this->Request()->getParam( "showAttributes" );

        // get the selection article
        /* @var $selectionArticle Configurator\Fieldset\Element\Article */
        $selectionArticle = $this->get( "shopware.model_manager" )
            ->getRepository( Configurator\Fieldset\Element\Article::class )
            ->find( $articleId );

        // get the article details
        $id          = $selectionArticle->getArticle()->getId();
        $ordernumber = $selectionArticle->getArticle()->getMainDetail()->getNumber();



        // get the configuration
        $config = $this->container->get( "atsd_configurator.configuration");

        // assign it
        $this->View()->assign( "atsdConfiguratorConfig", $config );



        // get the article
        $article = Shopware()->Modules()->Articles()->sGetArticleById( $id, null, $ordernumber );

        // assign it
        $this->View()->assign( "article", $article );

        // ...
        $arr = array(
            'status'          => $quickview,
            'showDescription' => $showDescription,
            'showAttributes'  => $showAttributes
        );

        // ...
        $this->View()->assign( "atsdConfiguratorQuickview", $arr );
    }







    /**
     * Add a configurator to the basket.
     *
     * @param boolean   $manual
     *
     * @throws Exception
     *
     * @return Selection
     */

    private function createSelectionFromRequest( $manual = false )
    {
        // get the component
        /* @var $component Components\AtsdConfigurator */
        $component = $this->get( "atsd_configurator.component" );

        /* @var $creatorService Components\Selection\CreatorService */
        $creatorService = $this->container->get( "atsd_configurator.selection.creator_service");

        // get parameters
        $configuratorId = (integer) $this->Request()->getParam( "configuratorId" );



        // articles as array
        $articles = array();

        // split it
        $split = (array) explode( ",", (string) $this->Request()->getParam( "selection" ) );

        // loop every
        foreach ( $split as $current )
        {
            // split again
            $aktu = (array) explode( ":", (string) $current );

            // add it
            $articles[(integer) $aktu[0]] = (integer) $aktu[1];
        }



        // get the configurator
        /* @var $configurator Configurator */
        $configurator = $component->getRepository()
            ->find( $configuratorId );

        // not found?
        if ( !$configurator instanceof Configurator )
            // not found
            throw new \Exception( "configurator with id " . $configuratorId . " not found" );

        // create a new selection
        $selection = $creatorService->createSelection( $configurator, $articles, $manual );

        // and return it
        return $selection;
    }








    /**
     * ...
     *
     * @return void
     */

    public function indexAction()
    {
        // get the components
        /* @var $session \Enlight_Components_Session_Namespace */
        $session   = $this->get( "session" );

        /* @var $db \Enlight_Components_Db_Adapter_Pdo_Mysql */
        $db        = $this->get( "shopware.db" );

        /* @var $component Components\AtsdConfigurator */
        $component = $this->get( "atsd_configurator.component" );



        // get the customer id (if logged in)
        $customerId = (integer) $session->offsetGet( "sUserId" );

        // not found?!
        if ( $customerId == 0 )
            // nothing to do
            return;

        // selections here
        $selections = array();

        // get the last 10 configurators
        $query = "
            SELECT a.id, UNIX_TIMESTAMP(a.`date`) AS timestamp, a.`key`, a.configuratorId
            FROM atsd_configurators_selections a
            WHERE a.customerId = :customerId
                AND a.manual = 1
            ORDER BY a.id DESC
            LIMIT 0,10
        ";
        $result = $db->fetchAll( $query, array( 'customerId' => $customerId ) );

        // loop them
        foreach ( $result as $aktu )
        {
            // get the selector
            /* @var $selection Selection */
            $selection = Shopware()->Models()
                ->getRepository( Selection::class )
                ->find( $aktu['id'] );

            // try it
            try
            {
                // get basket data
                $data = $component->getSelectionData( $selection );
            }
            catch ( ValidatorException $exception )
            {
                // ignore it
                continue;
            }

            // add stuff
            $data['date'] = $aktu['timestamp'];

            // add it
            array_push( $selections, $data );
        }

        // set to the view
        $this->View()->assign( "atsdConfiguratorSelections", $selections );

        // for the sidebar
        $this->View()->assign( "atsdConfiguratorActive", true );
        $this->View()->assign( "sUserLoggedIn", Shopware()->Modules()->Admin()->sCheckUser() );
    }







}