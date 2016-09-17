<?php

/**
 * Aquatuning Software Development - Configurator Plugin - Controller
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

class Shopware_Controllers_Frontend_AtsdConfigurator extends Enlight_Controller_Action
{


    /**
     * Save a new selection and redirect back to the article.
     *
     * @throws \Exception
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
     * @throws \Exception
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
            throw new \Exception( "unknown key" );

        // try to find the selection
        /* @var $selection \Shopware\CustomModels\AtsdConfigurator\Selection */
        $selection = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Selection' )
            ->findOneBy( array( 'key' => $key ) );

        // not found?
        if ( !$selection instanceof \Shopware\CustomModels\AtsdConfigurator\Selection )
            // nope
            throw new \Exception( "selection not found for key " . $key );



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
        /* @var $component \Shopware\AtsdConfigurator\Components\AtsdConfigurator */
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



        // validate it
        if ( $component->valdiateConfigurator( $configurator ) == false )
            // done
            throw new \Exception( "invalid configurator with id " . $selection->getConfigurator()->getId() );

        // filter the configurator
        $configurator = $component->filterConfigurator( $configurator );

        // get all product infos
        $configurator = $component->parseConfigurator( $configurator );



        // selector as array
        $selectionArr = array();

        // get articles
        $articles = $selection->getArticles();

        // loop it
        /* @var $article \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article */
        foreach ( $articles as $article )
            // add it
            array_push( $selectionArr, $article->getId() );



        // is the selection valid?
        if ( $component->validateSelection( $configurator, $selectionArr ) == false )
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



        // add it
        $component->addSelectionToBasket( $selection );

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



        // get the selection article
        /* @var $selectionArticle \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article */
        $selectionArticle = $this->get( "shopware.model_manager" )
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article' )
            ->find( $articleId );

        // get the article details
        $id          = $selectionArticle->getArticle()->getId();
        $ordernumber = $selectionArticle->getArticle()->getMainDetail()->getNumber();



        // get the bootstrap
        /* @var $bootstrap \Shopware_Components_Plugin_Bootstrap */
        $bootstrap = $this->get( "atsd_configurator.bootstrap" );

        // get the configuration
        $config = $bootstrap->Config()->toArray();

        // assign it
        $this->View()->assign( "atsdConfiguratorConfig", $config );



        // get the article
        $article = Shopware()->Modules()->Articles()->sGetArticleById( $id, null, $ordernumber );

        // assign it
        $this->View()->assign( "article", $article );
    }







    /**
     * Add a configurator to the basket.
     *
     * @param boolean   $manual
     *
     * @throws \Exception
     *
     * @return \Shopware\CustomModels\AtsdConfigurator\Selection
     */

    private function createSelectionFromRequest( $manual = false )
    {
        // get the component
        /* @var $component \Shopware\AtsdConfigurator\Components\AtsdConfigurator */
        $component = $this->get( "atsd_configurator.component" );

        // get parameters
        $configuratorId = (integer) $this->Request()->getParam( "configuratorId" );
        $articleIds     = (array)   explode( ",", (string) $this->Request()->getParam( "selection" ) );

        // get the configurator
        /* @var $configurator \Shopware\CustomModels\AtsdConfigurator\Configurator */
        $configurator = $component->getRepository()
            ->find( $configuratorId );

        // not found?
        if ( !$configurator instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator )
            // not found
            throw new \Exception( "configurator with id " . $configuratorId . " not found" );

        // create a new selection
        $selection = $component->createSelection( $configurator, $articleIds, $manual );

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

        /* @var $component \Shopware\AtsdConfigurator\Components\AtsdConfigurator */
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
            /* @var $selection \Shopware\CustomModels\AtsdConfigurator\Selection */
            $selection = Shopware()->Models()
                ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Selection' )
                ->find( $aktu['id'] );

            // get basket data
            $data = $component->getSelectionData( $selection );

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