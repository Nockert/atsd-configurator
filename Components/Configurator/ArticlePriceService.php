<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Components\Configurator;

use Shopware\Bundle\StoreFrontBundle\Struct;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class ArticlePriceService
{

    /**
     * ...
     *
     * @param Struct\ListProduct   $article
     * @param integer              $quantity
     *
     * @return Struct\Product\Price
     */

    private function getArticlePriceStructForQuantity( Struct\ListProduct $article, $quantity )
    {
        // loop every available price
        foreach ( $article->getPrices() as $price )
        {
            // get the rule
            $rule = $price->getRule();

            // final price?
            if ( $rule->getTo() === null )
                // we found it
                return $price;

            // found it?
            if ( ( $quantity >= $price->getFrom() ) and ( $quantity <= $price->getTo() ) )
                // yep
                return $price;
        }

        // none found?! get the cheapest
        return $article->getCheapestPrice();
    }



    /**
     * ...
     *
     * @param Struct\ListProduct   $article
     * @param integer              $quantity
     * @param integer              $rebate
     *
     * @return float
     */

    public function getArticlePrice( Struct\ListProduct $article, $quantity, $rebate = 0 )
    {
        // get the price struct
        $price = $this->getArticlePriceStructForQuantity( $article, $quantity );

        // calculate final price
        return $price->getCalculatedPrice() * $quantity * ( ( 100 - (integer) $rebate ) / 100 );
    }



    /**
     * ...
     *
     * @param Struct\ListProduct   $article
     * @param integer              $quantity
     * @param integer              $rebate
     *
     * @return float
     */

    public function getArticleNetPrice( Struct\ListProduct $article, $quantity, $rebate = 0 )
    {
        // get the price struct
        $price = $this->getArticlePriceStructForQuantity( $article, $quantity );

        // calculate final price
        return $price->getRule()->getPrice() * $quantity * ( ( 100 - (integer) $rebate ) / 100 );
    }



    /**
     * ...
     *
     * @param float   $price
     *
     * @return string
     */

    public function formatPrice( $price )
    {
        // return via context service
        return Shopware()->Modules()->Articles()->sFormatPrice( $price );
    }



    /**
     * Get the tax rate for a tax id.
     *
     * @param integer   $id
     *
     * @return float
     */

    public function getTaxRate( $id )
    {
        // call the sarticles module
        return (float) Shopware()->Modules()->Articles()->getTaxRateByConditions( $id );
    }

}
