<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components\Selection;

use Shopware\CustomModels\AtsdConfigurator\Repository;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class DefaultService
{

    /**
     * ...
     *
     * @var Repository
     */

    protected $repository;



    /**
     * ...
     *
     * @param Repository   $repository
     *
     * @return DefaultService
     */

    public function __construct( Repository $repository)
    {
        // set params
        $this->repository = $repository;
    }






    /**
     * Get a default selection for a specified configurator. We always use the first article
     * of every mandatory element.
     *
     * The returning selections key = element article id and value = quantity.
     *
     * Example:
     * array(
     *     1 => 15
     *     2 => 5
     * );
     *
     * @todo we have to parse and filter the configurator first or the first article which would
     *       be selected will be dropped in the filter process and the from price will be wrong
     *       because we -have- to select more mandatory articles
     *
     * @param integer   $configuratorId
     *
     * @return array
     */

    public function getDefaultSelection( $configuratorId )
    {
        // get builder to get the full configurator again
        $builder = $this->repository
            ->getPartialConfiguratorWithArticlesQueryBuilder();

        // just the one
        $builder->andWhere( "configurator.id = :configuratorId" )
            ->setParameter( "configuratorId", $configuratorId );

        // get all
        $configurators = $builder->getQuery()->getArrayResult();

        // the first
        $configurator = $configurators[0];



        // our selection
        $selection = array();

        // save the products back to the configurator
        foreach ( $configurator['fieldsets'] as $fieldset )
        {
            // loop the elements
            foreach ( $fieldset['elements'] as $element )
            {
                // is this optional?
                if ( $element['mandatory'] == false )
                    // ignore it
                    continue;

                // just set the first article of this element
                $selection[(integer) $element['articles'][0]['id']] = (integer) $element['articles'][0]['quantity'];
            }
        }

        // return the selection
        return $selection;
    }





}



