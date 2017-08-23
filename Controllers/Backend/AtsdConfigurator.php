<?php

/**
 * Aquatuning Software Development - Template Footer - Controller
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

use Shopware\CustomModels\AtsdConfigurator\Configurator;



class Shopware_Controllers_Backend_AtsdConfigurator extends Shopware_Controllers_Backend_ExtJs
{




    /**
     * Controller action method to get the list.
     *
     * @return void
     */

    public function getConfiguratorListAction()
    {
        // assign view
        $this->View()->assign(
            $this->getConfiguratorList(
                $this->Request()->getParam( "search", "" ),
                $this->Request()->getParam( "sort", array( array( 'property' => "id", 'direction' => "DESC" ) ) ),
                $this->Request()->getParam( "start", 0 ),
                $this->Request()->getParam( "limit", 25 )
            )
        );
    }






    /**
     * Controller action method to get the list.
     *
     * @return void
     */

    public function getArticleAvailableListAction()
    {
        // assign view
        $this->View()->assign(
            $this->getArticleAvailableList(
                $this->Request()->getParam( "search", "" ),
                $this->Request()->getParam( "sort", array( array( 'property' => "id", 'direction' => "ASC" ) ) ),
                $this->Request()->getParam( "start", 0 ),
                $this->Request()->getParam( "limit", 25 ),
                (integer) $this->Request()->getParam( "elementId", 0 )
            )
        );
    }







    /**
     * Get all items for the list.
     *
     * @param string    $search
     * @param string    $sort
     * @param integer   $offset
     * @param integer   $limit
     * @param integer   $elementId
     *
     * @return array
     */

    protected function getArticleAvailableList( $search, $sort, $offset, $limit, $elementId )
    {
        // fix sort
        $sort[0]['property'] = "detail." . $sort[0]['property'];

        // get the articles
        $builder = Shopware()->Models()->createQueryBuilder();

        // set it up
        $builder->select( array( 'PARTIAL article.{id,name}', 'PARTIAL detail.{id,number}' ) )
            ->from( '\Shopware\Models\Article\Article', "article" )
            ->leftJoin( "article.mainDetail", "detail" )
            ->orderBy( $sort[0]['property'], $sort[0]['direction'] );

        // set offset and limit
        $builder->setFirstResult( $offset )
            ->setMaxResults( $limit );

        // do we need to search?
        if ( !empty( $search ) )
        {
            // add the where append
            $builder->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->like( "article.name", ":search" ),
                    $builder->expr()->like( "detail.number", ":search" )
                )
            );

            // set the search parameter
            $builder->setParameter( "search", "%" . $search . "%" );
        }

        // no article from the element
        $builder->andWhere(
            '
            article.id NOT IN (
                SELECT configuratorSwArticle.id
                FROM \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article AS configuratorArticle
                    LEFT JOIN configuratorArticle.article AS configuratorSwArticle
                    LEFT JOIN configuratorArticle.element AS configuratorElement
                WHERE configuratorElement.id = ' . (integer) $elementId . '
            )
            '
        );



        // get the query object
        $query = $builder->getQuery();

        // set hydration mode
        $query->setHydrationMode( \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY );

        // use paginator to get the relevant items
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator( $query );

        // get total count of the query result
        $total = $paginator->count();

        // get an array copy of the paginator result
        $articles = $paginator->getIterator()->getArrayCopy();

        // prepare the data
        foreach ( $articles as &$article )
        {
            // add stuff
            $article['articleName']   = $article['name'];
            $article['articleNumber'] = $article['mainDetail']['number'];

            // remove stuff
            unset( $article['name'] );
            unset( $article['mainDetail'] );
        }

        // and return it
        return array(
            'success' => true,
            'total'   => $total,
            'data'    => $articles
        );
    }








    /**
     * Controller action method to get the list.
     *
     * @return void
     */

    public function getArticleAssignedListAction()
    {
        // assign view
        $this->View()->assign(
            $this->getArticleAssignedList(
                (integer) $this->Request()->getParam( "elementId", 0 )
            )
        );
    }









    /**
     * Get all items for the list.
     *
     * @param integer   $elementId
     *
     * @return array
     */

    protected function getArticleAssignedList( $elementId )
    {
        // get element
        /* @var $element \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element */
        $element = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element', $elementId );

        // not found?
        if ( !$element instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element )
            // nope
            return array(
                'success' => false
            );



        // get the fieldsets
        $builder = Shopware()->Models()->createQueryBuilder();

        // set it up
        $builder->select( array( "article", 'PARTIAL swArticle.{id,name}', 'PARTIAL swDetail.{id,number}' ) )
            ->from( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article', "article" )
            ->leftJoin( "article.article", "swArticle" )
            ->leftJoin( "swArticle.mainDetail", "swDetail" )
            ->where( "article.element= :element" )
            ->setParameter( "element", $element )
            ->orderBy( "article.position", "ASC" );

        // get them
        $articles = $builder->getQuery()->getArrayResult();



        // prepare the data
        foreach ( $articles as &$article )
        {
            // add stuff
            $article['articleName']      = $article['article']['name'];
            $article['articleNumber']    = $article['article']['mainDetail']['number'];
            $article['quantitySelect']   = (integer) $article['quantitySelect'];
            $article['quantityMultiply'] = (integer) $article['quantityMultiply'];

            // remove stuff
            unset( $article['article'] );
        }



        // and return it
        return array(
            'success' => true,
            'total'   => count( $articles ),
            'data'    => $articles
        );
    }








    /**
     * Controller action method to get the list.
     *
     * @return void
     */

    public function getFieldsetListAction()
    {
        // assign view
        $this->View()->assign(
            $this->getFieldsetList(
                (integer) $this->Request()->getParam( "configuratorId", 0 )
            )
        );
    }




    /**
     * Controller action method to get the list.
     *
     * @return void
     */

    public function getElementListAction()
    {
        // assign view
        $this->View()->assign(
            $this->getElementList(
                (integer) $this->Request()->getParam( "fieldsetId", 0 )
            )
        );
    }




    /**
     * Controller action method to get the list.
     *
     * @return void
     */

    public function getTemplateListAction()
    {
        // assign view
        $this->View()->assign(
            $this->getTemplateList()
        );
    }






    /**
     * Get all items for the list.
     *
     * @return array
     */

    protected function getTemplateList()
    {
        // get them
        $templates = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Template' )
            ->findAll();

        // to array
        $templates = Shopware()->Models()->toArray( $templates );

        // and return it
        return array(
            'success' => true,
            'total'   => count( $templates ),
            'data'    => $templates
        );
    }






    /**
     * Get all items for the list.
     *
     * @param string    $search
     * @param string    $sort
     * @param integer   $offset
     * @param integer   $limit
     *
     * @return array
     */

    protected function getConfiguratorList( $search, $sort, $offset, $limit )
    {
        // fix sort
        $sort[0]['property'] = "configurator." . $sort[0]['property'];

        // get the query builder
        /* @var $builder \Doctrine\ORM\QueryBuilder */
        $builder = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator' )
            ->getConfiguratorListQueryBuilder( $search, $sort, $offset, $limit );

        // get the query object
        $query = $builder->getQuery();

        // set hydration mode
        $query->setHydrationMode( \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY );

        // use paginator to get the relevant items
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator( $query );

        // get total count of the query result
        $total = $paginator->count();

        // get an array copy of the paginator result
        $configurators = $paginator->getIterator()->getArrayCopy();

        // prepare the data
        $configurators = $this->prepareConfiguratorList( $configurators );

        // and return it
        return array(
            'success' => true,
            'total'   => $total,
            'data'    => $configurators
        );
    }






    /**
     * ...
     *
     * @var array   $orders
     *
     * @return array
     */

    protected function prepareConfiguratorList( $configurators )
    {
        // ...
        foreach ( $configurators as &$configurator )
        {
            // force integer
            $configurator['chargeArticle'] = (integer) $configurator['chargeArticle'];

            // add the article
            $configurator['articleName']   = (string) $configurator['article']['name'];
            $configurator['articleNumber'] = (string) $configurator['article']['mainDetail']['number'];

            // remove relations
            unset( $configurator['article'] );
        }

        // ...
        return $configurators;
    }





    /**
     * Get all items for the list.
     *
     * @param integer   $configuratorId
     *
     * @return array
     */

    protected function getFieldsetList( $configuratorId )
    {
        // get configurator
        /* @var $configurator \Shopware\CustomModels\AtsdConfigurator\Configurator */
        $configurator = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator', $configuratorId );

        // not found?
        if ( !$configurator instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator )
            // nope
            return array(
                'success' => false
            );



        // get the fieldsets
        $builder = Shopware()->Models()->createQueryBuilder();

        // set it up
        $builder->select( array( "fieldset" ) )
            ->from( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset', "fieldset" )
            ->where( "fieldset.configurator = :configurator" )
            ->setParameter( "configurator", $configurator )
            ->orderBy( "fieldset.position", "ASC" );

        // get them
        $fieldsets = $builder->getQuery()->getArrayResult();

        // loop all fieldsets
        foreach ( $fieldsets as &$fieldset )
            // add configurator
            $fieldset['configuratorId'] = $configurator->getId();



        // and return it
        return array(
            'success' => true,
            'total'   => count( $fieldsets ),
            'data'    => $fieldsets
        );
    }





    /**
     * Get all items for the list.
     *
     * @param integer   $fieldsetId
     *
     * @return array
     */

    protected function getElementList( $fieldsetId )
    {
        // get fieldset
        /* @var $fieldset \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset */
        $fieldset = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset', $fieldsetId );

        // not found?
        if ( !$fieldset instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset )
            // nope
            return array(
                'success' => false
            );



        // get the fieldsets
        $builder = Shopware()->Models()->createQueryBuilder();

        // set it up
        $builder->select( array( "element", "template", "article" ) )
            ->from( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element', "element" )
            ->leftJoin( "element.template", "template" )
            ->leftJoin( "element.articles", "article" )
            ->where( "element.fieldset = :fieldset" )
            ->setParameter( "fieldset", $fieldset )
            ->orderBy( "element.position", "ASC" );

        // get them
        $elements = $builder->getQuery()->getArrayResult();



        // loop all fieldsets
        foreach ( $elements as &$element )
        {
            // add 1:n
            $element['fieldsetId']    = $fieldset->getId();
            $element['countArticles'] = count( $element['articles'] );
            $element['templateId']    = (integer) $element['template']['id'];
            $element['templateName']  = (string) $element['template']['name'];

            // make stuff integer
            $element['mandatory']  = (integer) $element['mandatory'];
            $element['multiple']   = (integer) $element['multiple'];
            $element['dependency'] = (integer) $element['dependency'];
            $element['surcharge']  = (integer) $element['surcharge'];

            // remove 1:n
            unset( $element['template'] );
            unset( $element['articles'] );
        }



        // and return it
        return array(
            'success' => true,
            'total'   => count( $elements ),
            'data'    => $elements
        );
    }







    /**
     * ...
     *
     * @return void
     */

    public function deleteFieldsetAction()
    {
        // assign view
        $this->View()->assign(
            // delete item
            $this->deleteFieldset(
                // get id
                (integer) $this->Request()->getParam( "id", 0 )
            )
        );
    }








    /**
     * Delete a single item.
     *
     * @param integer    $id
     *
     * @return array
     */

    protected function deleteFieldset( $id )
    {
        // try to find the fieldset
        /* @var $fieldset \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset */
        $fieldset = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset' )
            ->find( $id );

        // not found?
        if ( !$fieldset instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset )
            // nope
            return array(
                'success' => false,
                'error'   => "fieldset not found"
            );



        // try to delete the item
        try
        {
            // delete it
            Shopware()->Models()->remove( $fieldset );
            Shopware()->Models()->flush();

            // done
            return array(
                'success' => true
            );
        }
            // catch exception
        catch ( Exception $e )
        {
            // return error
            return array(
                'success' => false,
                'error'   => $e->getMessage()
            );
        }
    }






    /**
     * ...
     *
     * @return void
     */

    public function updateFieldsetAction()
    {
        // assign view
        $this->View()->assign(
            // update fieldset
            $this->updateFieldset(
                // get parameters
                $this->Request()->getParams()
            )
        );
    }




    /**
     * ...
     *
     * @return void
     */

    public function createFieldsetAction()
    {
        // assign view
        $this->View()->assign(
            // create fieldset
            $this->updateFieldset(
                // get parameters
                $this->Request()->getParams()
            )
        );
    }







    /**
     * Update/create an element.
     *
     * @param array   $data
     *
     * @return array
     */

    protected function updateFieldset( $data )
    {
        try
        {
            // new?
            if ( !isset( $data['id'] ) )
            {
                // find max id
                $query = "SELECT MAX(position) FROM atsd_configurators_fieldsets WHERE configuratorId = ?";
                $position = (integer) Shopware()->Db()->fetchOne( $query, array( (integer) $data['configuratorId'] ) );

                // get the configurator
                /* @var $configurator \Shopware\CustomModels\AtsdConfigurator\Configurator */
                $configurator = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator', (integer) $data['configuratorId'] );

                // not found?
                if ( !$configurator instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator )
                    // throw error
                    throw new Exception( "configurator not found" );

                // create a new one
                $fieldset = new \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset();

                // set it
                $fieldset->setConfigurator( $configurator );
                $fieldset->setPosition( $position + 1 );

                // persist it
                Shopware()->Models()->persist( $fieldset );
            }
            // edit an existing one
            else
            {
                // find it
                /* @var $fieldset \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset */
                $fieldset = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset', (integer) $data['id'] );

                // not found?
                if ( !$fieldset instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset )
                    // throw error
                    throw new Exception( "fieldset not found" );
            }



            // update
            $update = array(
                'name'        => (string)  $data['name'],
                'description' => (string)  $data['description'],
                'mediaFile'   => (string)  $data['mediaFile']
            );

            // update
            $fieldset->fromArray( $update );

            // now save it
            Shopware()->Models()->flush( $fieldset );

            // and return it
            return array(
                'success' => true
            );
        }
            // catch the exception
        catch ( Exception $e )
        {
            // return the error
            return array(
                'success' => false,
                'error'   => $e->getMessage()
            );
        }
    }








    /**
     * ...
     *
     * @return void
     */

    public function saveFieldsetPositionsAction()
    {
        // assign view
        $this->View()->assign(
            // get item list
            $this->saveFieldsetPositions(
                // get parameters
                json_decode( $this->Request()->getParam( "fieldsets" ) )
            )
        );
    }







    /**
     * ...
     *
     * @return void
     */

    public function saveArticlePositionsAction()
    {
        // assign view
        $this->View()->assign(
            $this->saveArticlePositions(
                json_decode( $this->Request()->getParam( "articles" ) )
            )
        );
    }






    /**
     * ...
     *
     * @param array   $articles
     *
     * @return array
     */

    public function saveArticlePositions( $articles )
    {
        // loop
        foreach ( $articles as $position )
        {
            // find it
            /* @var $article \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article */
            $article = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article', (integer) $position[0] );

            // not found?
            if ( !$article instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article )
                // next
                continue;

            // set position
            $article->setPosition( (integer) $position[1] );
        }



        // save everything
        Shopware()->Models()->flush();

        // and we re done
        return array(
            'success' => true
        );
    }








    /**
     * ...
     *
     * @param array   $fieldsets
     *
     * @return array
     */

    public function saveFieldsetPositions( $fieldsets )
    {
        // loop
        foreach ( $fieldsets as $position )
        {
            // find it
            /* @var $fieldset \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset */
            $fieldset = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset', (integer) $position[0] );

            // not found?
            if ( !$fieldset instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset )
                // next
                continue;

            // set position
            $fieldset->setPosition( (integer) $position[1] );
        }



        // save everything
        Shopware()->Models()->flush();

        // and we re done
        return array(
            'success' => true
        );
    }






    /**
     * ...
     *
     * @return void
     */

    public function saveElementPositionsAction()
    {
        // assign view
        $this->View()->assign(
            // get item list
            $this->saveElementPositions(
                // get parameters
                json_decode( $this->Request()->getParam( "elements" ) )
            )
        );
    }







    /**
     * ...
     *
     * @param array   $elements
     *
     * @return array
     */

    public function saveElementPositions( $elements )
    {
        // loop
        foreach ( $elements as $position )
        {
            // find it
            /* @var $element \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element */
            $element = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element', (integer) $position[0] );

            // not found?
            if ( !$element instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element )
                // next
                continue;

            // set position
            $element->setPosition( (integer) $position[1] );
        }



        // save everything
        Shopware()->Models()->flush();

        // and we re done
        return array(
            'success' => true
        );
    }









    /**
     * ...
     *
     * @return void
     */

    public function deleteElementAction()
    {
        // assign view
        $this->View()->assign(
            // delete item
            $this->deleteElement(
                // get id
                (integer) $this->Request()->getParam( "id", 0 )
            )
        );
    }






    /**
     * Delete a single item.
     *
     * @param integer    $id
     *
     * @return array
     */

    protected function deleteElement( $id )
    {
        // try to find the element
        /* @var $element \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element */
        $element = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element' )
            ->find( $id );

        // not found?
        if ( !$element instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element )
            // nope
            return array(
                'success' => false,
                'error'   => "element not found"
            );



        // try to delete the item
        try
        {
            // delete it
            Shopware()->Models()->remove( $element );
            Shopware()->Models()->flush();

            // done
            return array(
                'success' => true
            );
        }
            // catch exception
        catch ( Exception $e )
        {
            // return error
            return array(
                'success' => false,
                'error'   => $e->getMessage()
            );
        }
    }








    /**
     * ...
     *
     * @return void
     */

    public function updateElementAction()
    {
        // assign view
        $this->View()->assign(
            // update element
            $this->updateElement(
                // get parameters
                $this->Request()->getParams()
            )
        );
    }




    /**
     * ...
     *
     * @return void
     */

    public function createElementAction()
    {
        // assign view
        $this->View()->assign(
            // create fieldset
            $this->updateElement(
                // get parameters
                $this->Request()->getParams()
            )
        );
    }





    /**
     * ...
     *
     * @return void
     */

    public function updateArticleAction()
    {
        // assign view
        $this->View()->assign(
            $this->updateArticle(
                $this->Request()->getParams()
            )
        );
    }







    /**
     * ...
     *
     * @param array   $data
     *
     * @return array
     */

    protected function updateArticle( $data )
    {
        try
        {
            // find it
            /* @var $article \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article */
            $article = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article', (integer) $data['id'] );

            // not found?
            if ( !$article instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article )
                // throw error
                throw new Exception( "element not found" );



            // update
            $update = array(
                'quantity'         => (integer) $data['quantity'],
                'quantitySelect'   => (boolean) $data['quantitySelect'],
                'quantityMultiply' => ( ( (boolean) $data['quantityMultiply'] ) and ( (boolean) $data['quantitySelect'] ) ),
                'surcharge'        => (integer) $data['surcharge']
            );

            // do we have a surcharge?
            if ( $update['surcharge'] > 0 )
                // force only one
                $update = array_merge(
                    $update,
                    array( 'quantity' => 1, 'quantitySelect' => false, 'quantityMultiply' => false )
                );

            // update
            $article->fromArray( $update );

            // now save it
            Shopware()->Models()->flush( $article );

            // and return it
            return array(
                'success' => true
            );
        }
            // catch the exception
        catch ( Exception $e )
        {
            // return the error
            return array(
                'success' => false,
                'error'   => $e->getMessage()
            );
        }
    }








    /**
     * Update/create an element.
     *
     * @param array   $data
     *
     * @return array
     */

    protected function updateElement( $data )
    {
        try
        {
            // new?
            if ( !isset( $data['id'] ) )
            {
                // find max id
                $query = "SELECT MAX(position) FROM atsd_configurators_fieldsets_elements WHERE fieldsetId = ?";
                $position = (integer) Shopware()->Db()->fetchOne( $query, array( (integer) $data['fieldsetId'] ) );

                // get the fieldset
                /* @var $fieldset \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset */
                $fieldset = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset', (integer) $data['fieldsetId'] );

                // not found?
                if ( !$fieldset instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset )
                    // throw error
                    throw new Exception( "fieldset not found" );

                // create a new one
                $element = new \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element();

                // set it
                $element->setFieldset( $fieldset );
                $element->setPosition( $position + 1 );

                // persist it
                Shopware()->Models()->persist( $element );
            }
            // edit an existing one
            else
            {
                // find it
                /* @var $element \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element */
                $element = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element', (integer) $data['id'] );

                // not found?
                if ( !$element instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element )
                    // throw error
                    throw new Exception( "element not found" );
            }



            // find the template
            /* @var $template \Shopware\CustomModels\AtsdConfigurator\Template */
            $template = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Template', (integer) $data['templateId'] );



            // update
            $update = array(
                'name'        => (string)  $data['name'],
                'description' => (string)  $data['description'],
                'mediaFile'   => (string)  $data['mediaFile'],
                'mandatory'   => (boolean) $data['mandatory'],
                'multiple'    => (boolean) $data['multiple'],
                'dependency'  => (boolean) $data['dependency'],
                'surcharge'   => (boolean) $data['surcharge'],
                'comment'     => (string)  "",
                'template'    => $template
            );

            // force options for dependency
            if ( $update['dependency'] == true )
                // force
                $update = array_merge(
                    $update,
                    array( 'multiple' => true, 'mandatory' => false )
                );

            // update
            $element->fromArray( $update );

            // now save it
            Shopware()->Models()->flush( $element );

            // and return it
            return array(
                'success' => true
            );
        }
            // catch the exception
        catch ( Exception $e )
        {
            // return the error
            return array(
                'success' => false,
                'error'   => $e->getMessage()
            );
        }
    }







    /**
     * ....
     *
     * @return void
     */

    public function addElementArticleAction()
    {
        // assign view
        $this->View()->assign(
            $this->addElementArticle(
                $this->Request()->getParam( "recordId" ),
                json_decode( $this->Request()->getParam( "foreignIds" ) )
            )
        );
    }





    /**
     * ...
     *
     * @param integer   $elementId
     * @param array     $articleIds
     *
     * @return array
     */

    protected function addElementArticle( $elementId, $articleIds )
    {
        // pricelist id passed?
        if ( empty( $elementId ) )
            // nope
            return array(
                'success' => false,
                'error'   => "No element selected" );

        // any shops passed?
        if ( empty( $articleIds ) )
            // nope
            return array(
                'success' => false,
                'error'   => "No article(s) selected" );

        // get the element
        /* @var $element \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element */
        $element = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element' )
            ->find( $elementId );

        // not found?!
        if ( !$element instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element )
            // nope
            return array(
                'success' => false,
                'error'   => "No element selected" );

        // ...
        $configurator = $element->getFieldset()->getConfigurator();

        // main article to check every article against it (-1 will never be the same)
        $configuratorArticleId = ( $configurator->getArticle() instanceof \Shopware\Models\Article\Article )
            ? $configurator->getArticle()->getId()
            : -1;

        // count new shop
        $counter = 0;

        // find max id
        $query = "SELECT MAX(position) FROM atsd_configurators_fieldsets_elements_articles WHERE elementId = ?";
        $position = (integer) Shopware()->Db()->fetchOne( $query, array( (integer) $elementId ) ) + 1;

        // iterate all customers
        foreach ( $articleIds as $articleId )
        {
            // valid id?
            if ( empty( $articleId ) )
                // nope
                continue;

            // get our article
            /* @var $article \Shopware\Models\Article\Article */
            $article = Shopware()->Models()->find( '\Shopware\Models\Article\Article', $articleId );

            // not found?!
            if ( !$article instanceof \Shopware\Models\Article\Article )
                // invalid
                continue;

            // ...
            if ( $article->getId() == $configuratorArticleId )
                // ...
                continue;

            // create a new article
            $model = new \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article();

            // set it
            $model->setPosition( $position++ );
            $model->setArticle( $article );
            $model->setElement( $element );
            $model->setQuantity( 1 );
            $model->setQuantityMultiply( false );
            $model->setQuantitySelect( false );
            $model->setSurcharge( 0 );

            // persist it
            Shopware()->Models()->persist( $model );

            // count it
            $counter++;
        }

        // flush it to db
        Shopware()->Models()->flush();

        // done
        return array(
            'success' => true,
            'counter' => $counter );
    }





    /**
     * ....
     *
     * @return void
     */

    public function removeElementArticleAction()
    {
        // assign view
        $this->View()->assign(
            $this->removeElementArticle(
                $this->Request()->getParam( "recordId" ),
                json_decode( $this->Request()->getParam( "foreignIds" ) )
            )
        );
    }








    /**
     * ...
     *
     * @param integer   $elementId
     * @param array     $articleIds
     *
     * @return array
     */

    protected function removeElementArticle( $elementId, $articleIds )
    {
        // pricelist id passed?
        if ( empty( $elementId ) )
            // nope
            return array(
                'success' => false,
                'error'   => "No element selected" );

        // any shops passed?
        if ( empty( $articleIds ) )
            // nope
            return array(
                'success' => false,
                'error'   => "No article(s) selected" );

        // get the element
        /* @var $element \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element */
        $element = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element' )
            ->find( $elementId );

        // not found?!
        if ( !$element instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element )
            // nope
            return array(
                'success' => false,
                'error'   => "No element selected" );

        // count new shop
        $counter = 0;

        // iterate all customers
        foreach ( $articleIds as $articleId )
        {
            // valid id?
            if ( empty( $articleId ) )
                // nope
                continue;

            // get our article
            /* @var $article \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article */
            $article = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article', $articleId );

            // not found?!
            if ( !$article instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article )
                // invalid
                continue;

            // remove this article from selection
            $query = "
                DELETE FROM atsd_configurators_selections_articles
                WHERE articleId = ?
            ";
            Shopware()->Db()->query( $query, array( $article->getId() ) );

            // remove it
            Shopware()->Models()->remove( $article );

            // count it
            $counter++;
        }

        // flush it to db
        Shopware()->Models()->flush();

        // done
        return array(
            'success' => true,
            'counter' => $counter );
    }









    /**
     * ...
     *
     * @return void
     */

    public function updateConfiguratorAction()
    {
        // assign view
        $this->View()->assign(
            $this->updateConfigurator(
                $this->Request()->getParams()
            )
        );
    }




    /**
     * ...
     *
     * @return void
     */

    public function createConfiguratorAction()
    {
        // assign view
        $this->View()->assign(
            $this->updateConfigurator(
                $this->Request()->getParams()
            )
        );
    }







    /**
     * Update/create an element.
     *
     * @param array   $data
     *
     * @return array
     */

    protected function updateConfigurator( $data )
    {
        try
        {
            // new?
            if ( !isset( $data['id'] ) )
            {
                // create a new one
                $configurator = new \Shopware\CustomModels\AtsdConfigurator\Configurator();

                // persist it
                Shopware()->Models()->persist( $configurator );
            }
            // edit an existing one
            else
            {
                // find it
                /* @var $configurator \Shopware\CustomModels\AtsdConfigurator\Configurator */
                $configurator = Shopware()->Models()->find( '\Shopware\CustomModels\AtsdConfigurator\Configurator', (integer) $data['id'] );

                // not found?
                if ( !$configurator instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator )
                    // throw error
                    throw new Exception( "configurator not found" );
            }



            // try to find article
            $number = (string) $data['articleNumber'];

            // find it
            /* @var $detail \Shopware\Models\Article\Detail */
            $detail = Shopware()->Models()
                ->getRepository( '\Shopware\Models\Article\Detail' )
                ->findOneBy( array( 'number' => $number ) );

            // set article
            $article = ( $detail instanceof \Shopware\Models\Article\Detail )
                ? $detail->getArticle()
                : null;



            // try to find a configurator for this article
            if ( $article instanceof \Shopware\Models\Article\Article )
            {
                // read via sql query or it might fuck up our current doctrine configurator
                $query = '
                    SELECT id
                    FROM atsd_configurators
                    WHERE articleId = ?
                ';
                $confs = Shopware()->Db()->fetchAll( $query, array( $article->getId() ) );

                // did we find one?
                if ( count( $confs ) > 0 )
                {
                    // get the first
                    $conf = array_pop( $confs );

                    // is this NOT the same id?
                    if ( $conf['id'] != $configurator->getId() )
                        // return error
                        return array(
                            'success' => false,
                            'error'   => "Einem Artikel können nicht mehrere Konfiguratoren zugeordnet werden."
                        );
                }
            }



            // check if the main article is within the configurator as component article
            if ( $article instanceof \Shopware\Models\Article\Article )
            {
                $query = "
                    SELECT COUNT(*)
                    FROM atsd_configurators_fieldsets_elements_articles AS article 
                        LEFT JOIN atsd_configurators_fieldsets_elements AS element 
                            ON article.elementId = element.id
                        LEFT JOIN atsd_configurators_fieldsets AS fieldset 
                            ON element.fieldsetId = fieldset.id
                        LEFT JOIN atsd_configurators AS configurator
                            ON fieldset.configuratorId = configurator.id
                    WHERE configurator.id = ?
                        AND article.articleId = ?
                ";
                $count = (integer) Shopware()->Db()->fetchOne( $query, array( $configurator->getId(), $article->getId() ) );

                // ...
                if ( $count > 0 )
                    // return error
                    return array(
                        'success' => false,
                        'error'   => "Der Artikel ist bereits als Komponente im Konfigurator vorhanden."
                    );
            }



            // update
            $update = array(
                'name'          => (string)  $data['name'],
                'rebate'        => (integer) $data['rebate'],
                'chargeArticle' => (boolean) $data['chargeArticle'],
                'article'       =>           $article
            );

            // update
            $configurator->fromArray( $update );



            // now save it
            Shopware()->Models()->flush( $configurator );

            // and return it
            return array(
                'success' => true
            );
        }
            // catch the exception
        catch ( Exception $e )
        {
            // return the error
            return array(
                'success' => false,
                'error'   => $e->getMessage()
            );
        }
    }





    /**
     * ...
     *
     * @return void
     */

    public function deleteConfiguratorAction()
    {
        // assign view
        $this->View()->assign(
            $this->deleteConfigurator(
                (integer) $this->Request()->getParam( "id", 0 )
            )
        );
    }






    /**
     * Delete a single item.
     *
     * @param integer    $id
     *
     * @return array
     */

    protected function deleteConfigurator( $id )
    {
        // try to find the configurator
        /* @var $configurator \Shopware\CustomModels\AtsdConfigurator\Configurator */
        $configurator = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator' )
            ->find( $id );

        // not found?
        if ( !$configurator instanceof \Shopware\CustomModels\AtsdConfigurator\Configurator )
            // nope
            return array(
                'success' => false,
                'error'   => "configurator not found"
            );



        // get all selections
        /* @var $selections \Shopware\CustomModels\AtsdConfigurator\Selection[] */
        $selections = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Selection' )
            ->findBy( array( 'configurator' => $configurator ) );

        // any found?
        if ( count( $selections ) > 0 )
        {
            // loop them
            foreach ( $selections as $selection )
                // remove it
                Shopware()->Models()->remove( $selection );

            // save it
            Shopware()->Models()->flush();
        }



        // try to delete the item
        try
        {
            // delete it
            Shopware()->Models()->remove( $configurator );
            Shopware()->Models()->flush();

            // done
            return array(
                'success' => true
            );
        }
            // catch exception
        catch ( Exception $e )
        {
            // return error
            return array(
                'success' => false,
                'error'   => $e->getMessage()
            );
        }
    }




    /**
     * ...
     *
     * @return void
     */

    public function copyConfiguratorAction()
    {
        // assign view
        $this->View()->assign(
            $this->copyConfigurator(
                (integer) $this->Request()->getParam( "id", 0 )
            )
        );
    }





    /**
     * ...
     *
     * @param integer    $id
     *
     * @return array
     */

    protected function copyConfigurator( $id )
    {
        // ...
        /* @var $source Configurator */
        $source = Shopware()->Models()
            ->getRepository( '\Shopware\CustomModels\AtsdConfigurator\Configurator' )
            ->find( $id );

        // not found?
        if ( !$source instanceof Configurator )
            // nope
            return array(
                'success' => false,
                'error'   => "configurator not found"
            );



        // new configurator
        $target = new Configurator();

        // set it up
        $target->setName( $source->getName() . " - Kopie" );
        $target->setRebate( $source->getRebate() );
        $target->setChargeArticle( $source->getChargeArticle() );

        // save it
        $this->getModelManager()->persist( $target );
        $this->getModelManager()->flush( $target );



        // loop source fieldsets
        /* @var $sourceFieldset Configurator\Fieldset */
        foreach ( $source->getFieldsets() as $sourceFieldset )
        {
            // fieldset
            $fieldset = new Configurator\Fieldset();

            // set it
            $fieldset->setName( $sourceFieldset->getName() );
            $fieldset->setDescription( $sourceFieldset->getDescription() );
            $fieldset->setMediaFile( $sourceFieldset->getMediaFile() );
            $fieldset->setPosition( $sourceFieldset->getPosition() );
            $fieldset->setConfigurator( $target );

            // save
            $this->getModelManager()->persist( $fieldset );



            // loop elements
            /* @var $sourceElement Configurator\Fieldset\Element */
            foreach ( $sourceFieldset->getElements() as $sourceElement )
            {
                // element
                $element = new Configurator\Fieldset\Element();

                // set it up
                $element->setName( $sourceElement->getName() );
                $element->setDescription( $sourceElement->getDescription() );
                $element->setMediaFile( $sourceElement->getMediaFile() );
                $element->setPosition( $sourceElement->getPosition() );
                $element->setMandatory( $sourceElement->getMandatory() );
                $element->setMultiple( $sourceElement->getMutiple() );
                $element->setComment( $sourceElement->getComment() );
                $element->setTemplate( $sourceElement->getTemplate() );
                $element->setFieldset( $fieldset );

                // save
                $this->getModelManager()->persist( $element );



                // loop articles
                /* @var $sourceArticle Configurator\Fieldset\Element\Article */
                foreach ( $sourceElement->getArticles() as $sourceArticle )
                {
                    // article
                    $article = new Configurator\Fieldset\Element\Article();

                    // set it up
                    $article->setPosition( $sourceArticle->getPosition() );
                    $article->setQuantity( $sourceArticle->getQuantity() );
                    $article->setQuantitySelect( $sourceArticle->getQuantitySelect() );
                    $article->setQuantityMultiply( $sourceArticle->getQuantityMultiply() );
                    $article->setArticle( $sourceArticle->getArticle() );
                    $article->setElement( $element );

                    // save
                    $this->getModelManager()->persist( $article );
                }
            }
        }



        // save
        $this->getModelManager()->flush();

        // done
        return array(
            'success' => true
        );
    }





}
