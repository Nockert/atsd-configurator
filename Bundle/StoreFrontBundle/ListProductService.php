<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Bundle\StoreFrontBundle;

use Shopware\Components\DependencyInjection\Container;
use Shopware\CustomModels\AtsdConfigurator\Configurator;
use Shopware\AtsdConfigurator\Components\AtsdConfigurator as Component;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\AtsdConfigurator\Bundle\StoreFrontBundle\ConfiguratorService;
use Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;


/**
 * Aquatuning Software Development - Configurator - Component
 */

class ListProductService implements ListProductServiceInterface
{

    /**
     * The previously existing core service.
     *
     * @var ListProductServiceInterface
     */

    private $coreService;



    /**
     * The previously existing core service.
     *
     * @var ConfiguratorService
     */

    private $configuratorService;



    /**
     * The main component.
     *
     * @var Component
     */

    private $component;



    /**
     * DI container.
     *
     * @var Container
     */

    protected $container;



    /**
     * Object constructor.
     *
     * @param ListProductServiceInterface    $coreService
     * @param ConfiguratorService            $configuratorService
     * @param Component                      $component
     * @param Container                      $container
     *
     * @return ListProductService
     */

    public function __construct( ListProductServiceInterface $coreService, ConfiguratorService $configuratorService, Component $component, Container $container )
    {
        // set parameters
        $this->coreService         = $coreService;
        $this->configuratorService = $configuratorService;
        $this->component           = $component;
        $this->container           = $container;
    }




    /**
     * ...
     *
     * @param array                     $numbers
     * @param ProductContextInterface   $context
     *
     * @return ListProduct[]
     */

    public function getList( array $numbers, ProductContextInterface $context )
    {
        // call core service
        $products      = $this->coreService->getList( $numbers, $context );

        // get all configurators
        $configurators = $this->configuratorService->getList( $products );



        // loop all products
        foreach ( $products as $product )
        {
            // add attribute
            $product->addAttribute(
                "atsd_configurator",
                new Attribute( array(
                    'hasConfigurator'     => ( isset( $configurators[$product->getId()] ) ),
                    'defaultConfigurator' => ( isset( $configurators[$product->getId()] ) ) ? $this->getDefaultConfigurator( $configurators[$product->getId()]['id'], $product ) : null
                ) )
            );
        }



        // return the products
        return $products;
    }






    /**
     * ...
     *
     * @param string                    $number
     * @param ProductContextInterface   $context
     *
     * @return ListProduct
     */

    public function get( $number, ProductContextInterface $context )
    {
        // call our list
        $products = $this->getList( array( $number ), $context );

        // return first product
        return array_shift( $products );
    }







    /**
     * ...
     *
     * @param integer       $configuratorId
     * @param ListProduct   $product
     *
     * @return array
     */

    private function getDefaultConfigurator( $configuratorId, ListProduct $product )
    {
        // get the configurator
        /* @var $configurator Configurator */
        $configurator = $this->container->get( "shopware.model_manager" )->find( Configurator::class, $configuratorId );

        // call component
        $defaults = $this->component->getConfiguratorDefaults( $configuratorId );

        // add from our own article
        $defaults['price']       += ( $configurator->getChargeArticle() == true ) ? $product->getCheapestPrice()->getCalculatedPrice() : 0.0;
        $defaults['pseudoPrice'] += ( $configurator->getChargeArticle() == true ) ? $product->getCheapestPrice()->getCalculatedPrice() : 0.0;
        $defaults['stock']        = min( $defaults['stock'], $product->getStock() );

        // return it
        return $defaults;
    }





}


