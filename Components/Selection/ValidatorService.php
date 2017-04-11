<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components\Selection;

/**
 * Aquatuning Software Development - Configurator - Component
 */

class ValidatorService
{

    /**
     * ...
     *
     * @return ValidatorService
     */

    public function __construct()
    {
    }








    /**
     * Checks if the selection is complete for the configurator.
     *
     * @param array   $configurator
     * @param array   $selection
     *
     * @return boolean
     */

    public function validate( array $configurator, array $selection )
    {
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

                // loop the articles
                foreach ( $element['articles'] as $article )
                {
                    // did we select this article?
                    if ( isset( $selection[$article['id']] ) )
                    {
                        // @todo
                        // check quantity

                        // all good
                        continue 2;
                    }
                }

                // none of these mandatory articles are selected
                return false;
            }
        }

        // all good
        return true;
    }






}



