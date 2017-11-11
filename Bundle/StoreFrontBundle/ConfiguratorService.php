<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Bundle\StoreFrontBundle;

use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use AtsdConfigurator\Models\Repository;
use Shopware\Components\Model\ModelManager;
use AtsdConfigurator\Models\Configurator;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class ConfiguratorService
{

    /**
     * ...
     *
     * @var ModelManager
     */

    private $modelManager;



    /**
     * ...
     *
     * @var Repository
     */

    private $repository;



    /**
     * ...
     *
     * @param ModelManager   $modelManager
     */

    public function __construct( ModelManager $modelManager )
    {
        // set parameters
        $this->modelManager = $modelManager;
        $this->repository   = $modelManager->getRepository( Configurator::class );
    }



    /**
     * Get all bundles for the given products. The bundles are returned in an array
     * with the article id as key and the bundles as value.
     *
     * @param ListProduct[]   $products
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
        $builder = $this->repository->getMinimalConfiguratorQueryBuilder();

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
            function( ListProduct $product ) {
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
