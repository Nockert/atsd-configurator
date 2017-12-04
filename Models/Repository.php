<?php

/**
 * Aquatuning Software Development - Configurator - Model - Repository
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Models;

use Shopware\Components\Model\ModelRepository;
use Doctrine\ORM\QueryBuilder;



/**
 * Aquatuning Software Development - Configurator - Model - Repository
 */

class Repository extends ModelRepository
{



    /**
     * Get configurator.
     *
     * @return QueryBuilder
     */

    public function getMinimalConfiguratorQueryBuilder()
    {
        // get the query builder
        $builder = $this->getEntityManager()
            ->createQueryBuilder();

        // write the query
        $builder->select( array(
            "configurator", "linkedArticle"
        ) );

        // every table we need
        $builder->from( Configurator::class, "configurator" )
            ->leftJoin( "configurator.article", "linkedArticle" );

        // and return them
        return $builder;
    }





    /**
     * Get configurator.
     *
     * @return QueryBuilder
     */

    public function getConfiguratorQueryBuilder()
    {
        // get the query builder
        $builder = $this->getMinimalConfiguratorQueryBuilder();

        // write the query
        $builder->addSelect( array(
            "linkedArticleDetails", "fieldset", "element", "template"
        ) );

        // every table we need
        $builder->leftJoin( "linkedArticle.mainDetail", "linkedArticleDetails" )
            ->leftJoin( "configurator.fieldsets", "fieldset" )
            ->leftJoin( "fieldset.elements", "element" )
            ->leftJoin( "element.template", "template" );

        // set order
        $builder->addOrderBy( "fieldset.position", "ASC" )
            ->addOrderBy( "element.position", "ASC" );

        // and return them
        return $builder;
    }







    /**
     * Get full configurator.
     *
     * @return QueryBuilder
     */

    public function getConfiguratorWithArticlesQueryBuilder()
    {
        // get the query builder
        $builder = $this->getConfiguratorQueryBuilder();

        // write the query
        $builder->addSelect( array(
            "article", "swArticle", "swArticleDetail"
        ) );

        // every table we need;
        $builder->leftJoin( "element.articles", "article" )
            ->leftJoin( "article.article", "swArticle" )
            ->leftJoin( "swArticle.mainDetail", "swArticleDetail" );

        // and return them
        return $builder;
    }







    /**
     * Get full configurator.
     *
     * @return QueryBuilder
     */

    public function getPartialConfiguratorWithArticlesQueryBuilder()
    {
        // get the query builder
        $builder = $this->getConfiguratorWithArticlesQueryBuilder();

        // rewrite the complete select
        $builder->select( array(
            "configurator", "PARTIAL linkedArticle.{id}", "PARTIAL linkedArticleDetails.{id,number}", "fieldset", "element", "template", "article", "PARTIAL swArticle.{id,active,lastStock}", "PARTIAL swArticleDetail.{id,number,active,inStock}"
        ) );

        // and return them
        return $builder;
    }









    /**
     * Get the list.
     *
     * @param string    $search
     * @param array     $sort
     * @param integer   $offset
     * @param integer   $limit
     *
     * @return QueryBuilder
     */

    public function getConfiguratorListQueryBuilder( $search, $sort, $offset, $limit )
    {
        // get the query builder
        $builder = $this->getMinimalConfiguratorQueryBuilder();

        // add the details and just minimal article
        $builder->select( array(
            "configurator", "PARTIAL linkedArticle.{id,name}", "PARTIAL linkedArticleDetails.{id,number}"
        ) );

        // every table we need
        $builder->leftJoin( "linkedArticle.mainDetail", "linkedArticleDetails" );

        // set offset and limit
        $builder->setFirstResult( $offset )
            ->setMaxResults( $limit );

        // add order
        $builder->addOrderBy( $sort );

        // do we need to search?
        if ( !empty( $search ) )
        {
            // add the where append
            $builder->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->like( "configurator.name", ":search" ),
                    $builder->expr()->like( "linkedArticle.name", ":search" )
                )
            );

            // set the search parameter
            $builder->setParameter( "search", "%" . $search . "%" );
        }

        // return the builder
        return $builder;
    }






    /**
     * Get the articles.
     *
     * @deprecated not used by any other method
     *
     * @return QueryBuilder
     */

    public function getSelectionArticlesQueryBuilder()
    {
        // get the query builder
        $builder = $this->getEntityManager()
            ->createQueryBuilder();

        // write the query
        $builder->select( array(
            "selection", "PARTIAL selectionArticle.{id,quantity}", "PARTIAL configuratorArticle.{id,quantity}", "PARTIAL swArticle.{id}", "PARTIAL swArticleDetail.{id,number}"
        ) );

        // every table we need
        $builder->from( Selection::class, "selection" )
            ->leftJoin( "selection.articles", "selectionArticle" )
            ->leftJoin( "selectionArticle.articles", "configuratorArticle" )
            ->leftJoin( "configuratorArticle.article", "swArticle" )
            ->leftJoin( "swArticle.mainDetail", "swArticleDetail" );

        // and return them
        return $builder;
    }




}


