<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Bundle\StoreFrontBundle;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class ConfiguratorService
{

    /**
     * Shopware model manager.
     *
     * @var \Shopware\Components\Model\ModelManager
     */

    private $modelManager;



    /**
     * Plugin component.
     *
     * @var \Shopware\AtsdConfigurator\Components\AtsdConfigurator
     */

    private $component;



    /**
     * DI container.
     *
     * @var \Shopware\Components\DependencyInjection\Container
     */

    private $container;





    /**
     * Object constructor.
     *
     * @param \Shopware\Components\Model\ModelManager                  $modelManager
     * @param \Shopware\AtsdConfigurator\Components\AtsdConfigurator   $component
     * @param \Shopware\Components\DependencyInjection\Container       $container
     *
     * @return \Shopware\AtsdConfigurator\Bundle\StoreFrontBundle\ConfiguratorService
     */

    public function __construct(
        \Shopware\Components\Model\ModelManager $modelManager,
        \Shopware\AtsdConfigurator\Components\AtsdConfigurator $component,
        \Shopware\Components\DependencyInjection\Container $container )
    {
        // set parameters
        $this->modelManager = $modelManager;
        $this->component    = $component;
        $this->container    = $container;
    }







    /**
     * Get all bundles for the given products. The bundles are returned in an array
     * with the article id as key and the bundles as value.
     *
     * @param \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct[]   $products
     *
     * @return array
     */

    public function getList( array $products )
    {
        // get article ids
        $articleIds = $this->getArticleId( $products );

        // no articles given?
        if ( count( $articleIds ) == 0 )
            // done
            return array();



        // get query builder
        $builder = $this->component->getRepository()->getMinimalConfiguratorQueryBuilder();

        // only for our products
        $builder->andWhere(
            $builder->expr()->in( "linkedArticle.id", $articleIds )
        );

        // get the results as array
        $configurators = $builder->getQuery()->getArrayResult();



        // return array with article id as key
        $return = array();

        // loop the bundles
        foreach ( $configurators as $configurator )
        {
            // get article id
            $id = $configurator['article']['id'];

            // set it
            $return[$id] = $configurator;
        }



        // return them
        return $return;
    }








    /**
     * ...
     *
     * @param array   $products
     *
     * @return array
     */

    private function getArticleId( array $products )
    {
        // get the ids
        $articleIds = array_map(
            function( \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct $product ) {
                return $product->getId();
            },
            $products
        );

        // unique the ids
        $articleIds = array_unique( array_values( $articleIds ) );

        // return them
        return $articleIds;
    }


}


