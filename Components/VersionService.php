<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components;

/**
 * Aquatuning Software Development - Configurator - Component
 */

class VersionService
{



    /**
     * Compare versions.
     *
     * @param string   $version   Like: 5.0.0
     * @param string   $operator  Like: <=
     *
     * @return mixed
     */

    private function versionCompare( $version, $operator )
    {
        // return by default version compare
        return version_compare( Shopware()->Config()->get( 'Version' ), $version, $operator );
    }




    /**
     * ...
     *
     * @return boolean
     */

    public function isShopware51()
    {
        // return it
        return ( ( $this->versionCompare( '5.1.0', '>=' ) ) and ( $this->versionCompare( '5.2.0', '<' ) ) );
    }




    /**
     * ...
     *
     * @return boolean
     */

    public function isShopware52()
    {
        // return it
        return $this->versionCompare( '5.2.0', '>=' );
    }




}



